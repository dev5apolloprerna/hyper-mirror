<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductFeature;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductFeatureController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductFeature::query()->where('isDelete', 0);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('feature_name', 'like', "%{$search}%");
        }

        $features = $query->orderByDesc('feature_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product-feature.index', compact('features'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'feature_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_feature', 'feature_name')->where(fn ($query) => $query->where('isDelete', 0)),
            ],
        ]);

        ProductFeature::create([
            'feature_name' => $request->feature_name,
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        return redirect()->route('admin.product-feature.index')
            ->with('success', 'Product feature added successfully.');
    }

    public function update(Request $request, $id)
    {
        $feature = ProductFeature::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'feature_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_feature', 'feature_name')
                    ->where(fn ($query) => $query->where('isDelete', 0))
                    ->ignore($feature->feature_id, 'feature_id'),
            ],
        ]);

        $feature->update([
            'feature_name' => $request->feature_name,
        ]);

        return redirect()->route('admin.product-feature.index')
            ->with('success', 'Product feature updated successfully.');
    }

    public function destroy($id)
    {
        $feature = ProductFeature::where('isDelete', 0)->findOrFail($id);

        $feature->update([
            'iStatus' => 0,
            'isDelete' => 1,
        ]);

        return redirect()->route('admin.product-feature.index')
            ->with('success', 'Product feature deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:product_feature,feature_id',
        ]);

        ProductFeature::where('isDelete', 0)
            ->whereIn('feature_id', $request->ids)
            ->update([
                'iStatus' => 0,
                'isDelete' => 1,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Selected features deleted successfully.',
        ]);
    }
}
