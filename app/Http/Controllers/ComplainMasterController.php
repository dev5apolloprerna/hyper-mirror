<?php

namespace App\Http\Controllers;

use App\Models\ComplainMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplainMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = ComplainMaster::query()
            ->where('isDelete', 0)
            ->latest('complain_id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(15)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    public function store(Request $request)
    {
        if (optional($request->user()->crmRole)->slug === 'account') {
            return redirect()->route('complaints.index')
                ->with('error', 'Account users can only resolve complaints.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'name' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:190',
        ]);

        $user = $request->user();

        ComplainMaster::create([
            'irole_id' => $user?->iRoalId,
            'user_id' => $user?->id,
            'name' => $validated['name'] ?? $user?->full_name ?? $user?->strUserName,
            'email' => $validated['email'] ?? $user?->email,
            'comment' => $validated['comment'],
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'pending',
            'iStatus' => 1,
            'isDelete' => 0,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted successfully.');
    }

    public function resolve(Request $request, ComplainMaster $complaint)
    {
        $validated = $request->validate([
            'resolve_comment' => 'required|string|max:2000',
            'resolve_date' => 'required|date',
            'payment_type' => ['required', Rule::in(['cash', 'online'])],
            'amount' => 'required|numeric|min:0',
        ]);

        $complaint->update([
            'status' => 'resolved',
            'resolve_user_id' => $request->user()->id,
            'resolve_comment' => $validated['resolve_comment'],
            'resolve_date' => $validated['resolve_date'],
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint resolved successfully.');
    }
}
