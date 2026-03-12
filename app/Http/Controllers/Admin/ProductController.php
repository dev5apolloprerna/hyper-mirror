<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('strProductName', 'like', "%{$search}%")
                  ->orWhere('MRP', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($sub) use ($search) {
                      $sub->where('strCategoryName', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->orderBy('iProductId', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('strCategoryName', 'asc')->get();
        return view('admin.product.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'iCategoryId'    => 'required|exists:product_categories,iCategoryId',
            'strProductName' => 'required|string|max:100',
            'MRP'            => 'required|integer|min:0',
        ]);

        Product::create([
            'iCategoryId'    => $request->iCategoryId,
            'strProductName' => $request->strProductName,
            'MRP'            => $request->MRP,
        ]);

        return redirect()->route('admin.product.index')
            ->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ProductCategory::orderBy('strCategoryName', 'asc')->get();

        return view('admin.product.form', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'iCategoryId'    => 'required|exists:product_categories,iCategoryId',
            'strProductName' => 'required|string|max:100',
            'MRP'            => 'required|integer|min:0',
        ]);

        $product->update([
            'iCategoryId'    => $request->iCategoryId,
            'strProductName' => $request->strProductName,
            'MRP'            => $request->MRP,
        ]);

        return redirect()->route('admin.product.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.product.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:products,iProductId',
        ]);

        Product::whereIn('iProductId', $request->ids)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected products deleted successfully.'
        ]);
    }
}
