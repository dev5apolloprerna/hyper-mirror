<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('strCategoryName', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('iCategoryId', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product-category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.product-category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'strCategoryName' => 'required|string|max:50|unique:product_categories,strCategoryName',
        ]);

        ProductCategory::create([
            'strCategoryName' => $request->strCategoryName,
        ]);

        return redirect()->route('admin.product-category.index')
            ->with('success', 'Product category added successfully.');
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        return view('admin.product-category.form', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $request->validate([
            'strCategoryName' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_categories', 'strCategoryName')->ignore($category->iCategoryId, 'iCategoryId')
            ],
        ]);

        $category->update([
            'strCategoryName' => $request->strCategoryName,
        ]);

        return redirect()->route('admin.product-category.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.product-category.index')
            ->with('success', 'Product category deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:product_categories,iCategoryId',
        ]);

        ProductCategory::whereIn('iCategoryId', $request->ids)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected categories deleted successfully.'
        ]);
    }
}
