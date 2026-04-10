<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Listing ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorise();

        $query = Invoice::with(['showroom', 'createdBy', 'items.category', 'items.product'])
            ->orderByDesc('iInvoiceId');

        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('InvoiceDate', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('InvoiceDate', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                  ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));
            });
        }

        $invoices  = $query->paginate(15)->withQueryString();
        $showrooms = Showroom::orderBy('strShowRoomName')->get();

        // Grand totals for filtered set (no pagination)
        $totalQuery = Invoice::query();
        if ($request->filled('iShowroomId'))  $totalQuery->where('iShowroomId', $request->iShowroomId);
        if ($request->filled('from_date'))    $totalQuery->whereDate('InvoiceDate', '>=', $request->from_date);
        if ($request->filled('to_date'))      $totalQuery->whereDate('InvoiceDate', '<=', $request->to_date);
        if ($request->filled('search')) {
            $s = trim($request->search);
            $totalQuery->where(function ($q) use ($s) {
                $q->where('strInvoiceNo', 'like', "%{$s}%")
                  ->orWhereHas('showroom', fn($sub) => $sub->where('strShowRoomName', 'like', "%{$s}%"));
            });
        }
        $filteredIds    = $totalQuery->pluck('iInvoiceId');
        $grandTotal     = (float) InvoiceItem::whereIn('iInvoiceId', $filteredIds)->sum('iAmount');
        $totalInvoices  = $filteredIds->count();

        return view('store-manager.invoice.index', compact(
            'invoices', 'showrooms',
            'grandTotal', 'totalInvoices'
        ));
    }

    // ── Create form ──────────────────────────────────────────────────────────
    public function create()
    {
        $this->authorise();

        $assignedShowroomIds = auth()->user()
            ->showrooms()
            ->pluck('showrooms.iShowroomId');

        $showrooms = Showroom::query()
            ->when($assignedShowroomIds->isNotEmpty(), function ($query) use ($assignedShowroomIds) {
                $query->whereIn('iShowroomId', $assignedShowroomIds);
            })
            ->orderBy('strShowRoomName')
            ->get();

        $defaultShowroomId = old('iShowroomId')
            ?: ($showrooms->count() === 1 ? $showrooms->first()->iShowroomId : null);


        $categories = ProductCategory::orderBy('strCategoryName')->get();
        $products   = Product::orderBy('strProductName')->get();

        return view('store-manager.invoice.create', compact('showrooms', 'categories', 'products', 'defaultShowroomId'));
    }

    // ── Store ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorise();

        $assignedShowroomIds = auth()->user()
            ->showrooms()
            ->pluck('showrooms.iShowroomId')
            ->all();

        $request->validate([
        'iShowroomId'              => ['required', 'exists:showrooms,iShowroomId', Rule::in($assignedShowroomIds)],
        'InvoiceDate'              => 'required|date',
        'strNotes'                 => 'nullable|string|max:500',
        'items'                    => 'required|array|min:1',
        'items.*.iCategoryId'      => 'required|exists:product_categories,iCategoryId',
        'items.*.iProductId'       => 'required|exists:products,iProductId',
        'items.*.quantity'         => 'required|integer|min:1',
        'items.*.unit_price'       => 'required|numeric|min:0',
        ], [
            'iShowroomId.in' => 'Please select your assigned showroom.',
        ]);

        DB::beginTransaction();

        try {
            // Generate invoice number: INV-YYYYMM-XXXX
            $prefix    = 'INV-' . now()->format('Ym') . '-';
            $lastCount = Invoice::where('strInvoiceNo', 'like', $prefix . '%')->count();
            $invoiceNo = $prefix . str_pad($lastCount + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'strInvoiceNo' => $invoiceNo,
                'iShowroomId'  => $request->iShowroomId,
                'iCreatedBy'   => auth()->id(),
                'InvoiceDate'  => $request->InvoiceDate,
                'strNotes'     => $request->strNotes,
                'status'       => 'confirmed',
            ]);

            foreach ($request->items as $row) {
                $qty    = (int)   $row['quantity'];
                $price  = (float) $row['unit_price'];
                $amount = $qty * $price;

                InvoiceItem::create([
                    'iInvoiceId'  => $invoice->iInvoiceId,
                    'iCategoryId' => $row['iCategoryId'],
                    'iProductId'  => $row['iProductId'],
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'iAmount'     => $amount,
                ]);
            }

            DB::commit();

            return redirect()->route('store.invoice.index')
                ->with('success', "Invoice {$invoiceNo} created successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }
     public function pdf(Invoice $invoice)
    {
        $this->authorise();
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product']);

        $pdf = Pdf::loadView('store-manager.invoice.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream($invoice->strInvoiceNo . '.pdf');
    }


    // ── View single invoice ──────────────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $this->authorise();
        $invoice->load(['showroom', 'createdBy', 'items.category', 'items.product']);
        return view('store-manager.invoice.show', compact('invoice'));
    }

    // ── Delete ───────────────────────────────────────────────────────────────
    public function destroy(Invoice $invoice)
    {
        $this->authorise();
        $invoice->delete();
        return redirect()->route('store.invoice.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    // ── AJAX: products by category ───────────────────────────────────────────
    public function productsByCategory(Request $request)
    {
        $this->authorise();
        $products = Product::where('iCategoryId', $request->category_id)
            ->orderBy('strProductName')
            ->get(['iProductId', 'strProductName', 'MRP']);
        return response()->json($products);
    }

    // ── Guard ────────────────────────────────────────────────────────────────
    private function authorise(): void
    {
        abort_unless(optional(auth()->user()->crmRole)->slug === 'storemanager', 403);
    }
}
