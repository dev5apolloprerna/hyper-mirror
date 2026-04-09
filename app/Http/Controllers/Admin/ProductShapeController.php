<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductShape;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductShapeController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductShape::query()->where('isDelete', 0);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('shape_title', 'like', "%{$search}%");
        }

        $shapes = $query->orderByDesc('shape_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product-shape.index', compact('shapes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shape_title' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_shape', 'shape_title')->where(fn ($query) => $query->where('isDelete', 0)),
            ],
        ]);

        ProductShape::create([
            'shape_title' => $request->shape_title,
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        return redirect()->route('admin.product-shape.index')
            ->with('success', 'Product shape added successfully.');
    }

    public function update(Request $request, $id)
    {
        $shape = ProductShape::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'shape_title' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_shape', 'shape_title')
                    ->where(fn ($query) => $query->where('isDelete', 0))
                    ->ignore($shape->shape_id, 'shape_id'),
            ],
        ]);

        $shape->update([
            'shape_title' => $request->shape_title,
        ]);

        return redirect()->route('admin.product-shape.index')
            ->with('success', 'Product shape updated successfully.');
    }

    public function destroy($id)
    {
        $shape = ProductShape::where('isDelete', 0)->findOrFail($id);

        $shape->update([
            'iStatus' => 0,
            'isDelete' => 1,
        ]);

        return redirect()->route('admin.product-shape.index')
            ->with('success', 'Product shape deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:product_shape,shape_id',
        ]);

        ProductShape::where('isDelete', 0)
            ->whereIn('shape_id', $request->ids)
            ->update([
                'iStatus' => 0,
                'isDelete' => 1,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Selected shapes deleted successfully.',
        ]);
    }
}
