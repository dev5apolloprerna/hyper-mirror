<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductStock::with(['product.category', 'showroom']);

        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('strProductName', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('strCategoryName', 'like', "%{$search}%");
                        });
                })->orWhereHas('showroom', function ($showroomQuery) use ($search) {
                    $showroomQuery->where('strShowRoomName', 'like', "%{$search}%");
                });
            });
        }

        $stocks = $query->orderByDesc('iProductStockId')->paginate(15)->withQueryString();
        $salesByStock = $this->salesByStock($stocks->pluck('iProductId'), $stocks->pluck('iShowroomId'));

        $totals = [
            'inside' => (int) ProductStock::sum('inside_quantity'),
            'showroom' => (int) ProductStock::sum('showroom_quantity'),
            'sales' => (int) InvoiceItem::join('invoices', 'invoice_items.iInvoiceId', '=', 'invoices.iInvoiceId')
                ->where('invoices.status', '!=', 'cancelled')
                ->sum('invoice_items.quantity'),
        ];
        $totals['available'] = $totals['inside'] + $totals['showroom'] - $totals['sales'];

        $products = Product::with('category')->orderBy('strProductName')->get();
        $showrooms = Showroom::orderBy('strShowRoomName')->get();

        return view('admin.stock.index', compact('stocks', 'products', 'showrooms', 'salesByStock', 'totals'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        ProductStock::create($validated);

        return redirect()->route('admin.stock.index')->with('success', 'Stock added successfully.');
    }

    public function update(Request $request, ProductStock $stock)
    {
        $validated = $this->validatedData($request, $stock->iProductStockId);
        $stock->update($validated);

        return redirect()->route('admin.stock.index')->with('success', 'Stock updated successfully.');
    }

    public function destroy(ProductStock $stock)
    {
        $stock->delete();

        return redirect()->route('admin.stock.index')->with('success', 'Stock deleted successfully.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'iProductId' => [
                'required',
                'exists:products,iProductId',
                Rule::unique('product_stocks', 'iProductId')
                    ->where('iShowroomId', $request->iShowroomId)
                    ->ignore($ignoreId, 'iProductStockId'),
            ],
            'iShowroomId' => 'required|exists:showrooms,iShowroomId',
            'inside_quantity' => 'required|integer|min:0',
            'showroom_quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string|max:1000',
        ]);
    }

    private function salesByStock($productIds, $showroomIds)
    {
        return InvoiceItem::select('invoice_items.iProductId', 'invoices.iShowroomId', DB::raw('SUM(invoice_items.quantity) as sold_quantity'))
            ->join('invoices', 'invoice_items.iInvoiceId', '=', 'invoices.iInvoiceId')
            ->where('invoices.status', '!=', 'cancelled')
            ->whereIn('invoice_items.iProductId', $productIds->filter()->unique())
            ->whereIn('invoices.iShowroomId', $showroomIds->filter()->unique())
            ->groupBy('invoice_items.iProductId', 'invoices.iShowroomId')
            ->get()
            ->keyBy(fn ($row) => $row->iProductId . '-' . $row->iShowroomId);
    }
}
