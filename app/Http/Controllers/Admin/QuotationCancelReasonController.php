<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationCancelReason;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuotationCancelReasonController extends Controller
{
    public function index(Request $request)
    {
        $query = QuotationCancelReason::query()->where('isDelete', 0);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('reason', 'like', "%{$search}%");
        }

        $reasons = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.quotation-cancel-reason.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => [
                'required',
                'string',
                'max:150',
                Rule::unique('quotation_cancel_reasons', 'reason')->where(fn ($query) => $query->where('isDelete', 0)),
            ],
        ]);

        QuotationCancelReason::create([
            'reason' => $request->reason,
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        return redirect()->route('admin.quotation-cancel-reason.index')
            ->with('success', 'Quotation cancel reason added successfully.');
    }

    public function update(Request $request, $id)
    {
        $cancelReason = QuotationCancelReason::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'reason' => [
                'required',
                'string',
                'max:150',
                Rule::unique('quotation_cancel_reasons', 'reason')
                    ->where(fn ($query) => $query->where('isDelete', 0))
                    ->ignore($cancelReason->id),
            ],
        ]);

        $cancelReason->update([
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.quotation-cancel-reason.index')
            ->with('success', 'Quotation cancel reason updated successfully.');
    }

    public function destroy($id)
    {
        $cancelReason = QuotationCancelReason::where('isDelete', 0)->findOrFail($id);

        $cancelReason->update([
            'iStatus' => 0,
            'isDelete' => 1,
        ]);

        return redirect()->route('admin.quotation-cancel-reason.index')
            ->with('success', 'Quotation cancel reason deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:quotation_cancel_reasons,id',
        ]);

        QuotationCancelReason::where('isDelete', 0)
            ->whereIn('id', $request->ids)
            ->update([
                'iStatus' => 0,
                'isDelete' => 1,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Selected reasons deleted successfully.',
        ]);
    }
}
