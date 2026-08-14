<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use App\Models\Showroom;
use App\Models\StockMovement;
use App\Support\StockManager;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Current stock levels, one row per product per showroom that has ever
     * had a movement (products with no stock activity anywhere are not shown
     * here since there is nothing to display for them yet).
     */
    public function index(Request $request)
    {
        $query = ProductStock::with(['product.category', 'showroom']);

        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('iCategoryId')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('iCategoryId', $request->iCategoryId);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('strProductName', 'like', "%{$search}%");
            });
        }

        $stocks = $query->orderBy('iShowroomId')
            ->paginate(15)
            ->withQueryString();

        $products   = Product::orderBy('strProductName')->get();
        $showrooms  = Showroom::orderBy('strShowRoomName')->get();
        $categories = ProductCategory::orderBy('strCategoryName')->get();

        return view('admin.stock.index', compact('stocks', 'products', 'showrooms', 'categories'));
    }

    /**
     * Movement ledger / history, optionally filtered.
     */
    public function ledger(Request $request)
    {
        $query = StockMovement::with(['product', 'showroom', 'relatedShowroom', 'createdBy']);

        if ($request->filled('iProductId')) {
            $query->where('iProductId', $request->iProductId);
        }

        if ($request->filled('iShowroomId')) {
            $query->where('iShowroomId', $request->iShowroomId);
        }

        if ($request->filled('strType')) {
            $query->where('strType', $request->strType);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $movements = $query->orderBy('iMovementId', 'desc')
            ->paginate(20)
            ->withQueryString();

        $products  = Product::orderBy('strProductName')->get();
        $showrooms = Showroom::orderBy('strShowRoomName')->get();

        return view('admin.stock.ledger', compact('movements', 'products', 'showrooms'));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'iProductId'  => 'required|exists:products,iProductId',
            'iShowroomId' => 'required|exists:showrooms,iShowroomId',
            'quantity'    => 'required|integer|min:1',
            'reason'      => 'nullable|string|max:255',
        ]);

        StockManager::stockIn(
            (int) $request->iProductId,
            (int) $request->iShowroomId,
            (int) $request->quantity,
            $request->reason,
            auth()->id()
        );

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock added successfully.');
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'iProductId'  => 'required|exists:products,iProductId',
            'iShowroomId' => 'required|exists:showrooms,iShowroomId',
            'quantity'    => 'required|integer|min:1',
            'reason'      => 'nullable|string|max:255',
        ]);

        try {
            StockManager::stockOut(
                (int) $request->iProductId,
                (int) $request->iShowroomId,
                (int) $request->quantity,
                $request->reason,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.stock.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock removed successfully.');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'iProductId'      => 'required|exists:products,iProductId',
            'iFromShowroomId' => 'required|exists:showrooms,iShowroomId|different:iToShowroomId',
            'iToShowroomId'   => 'required|exists:showrooms,iShowroomId',
            'quantity'        => 'required|integer|min:1',
            'reason'          => 'nullable|string|max:255',
        ]);

        try {
            StockManager::transfer(
                (int) $request->iProductId,
                (int) $request->iFromShowroomId,
                (int) $request->iToShowroomId,
                (int) $request->quantity,
                $request->reason,
                auth()->id()
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.stock.index')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock transferred successfully.');
    }
}
