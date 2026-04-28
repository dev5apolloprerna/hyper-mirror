<?php

namespace App\Http\Controllers;

use App\Models\ComplainMaster;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LedgerHelper;
use Illuminate\Support\Facades\DB;

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
                    ->orWhere('invoice_no', 'like', "%{$search}%")
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
            'invoice_no' => 'nullable|string|max:50|exists:leads,strLeadNo',
        ], [
            'invoice_no.exists' => 'The entered number was not found in quotation numbers.',
        ]);

        $user = $request->user();

        ComplainMaster::create([
            'irole_id' => $user?->iRoalId,
            'user_id' => $user?->id,
            'name' => $validated['name'] ?? $user?->full_name ?? $user?->strUserName,
            'email' => $validated['email'] ?? null,
            'comment' => $validated['comment'],
            'phone' => $request->phone,
            'invoice_no' => $validated['invoice_no'] ?? null,
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

        $auth = Auth::user()->id;
        $Cr_emp_id = $auth;
        $invoices_Id  =  $complaint->complain_id ?? 0;
        $amount = $request->amount;
        $dr_emp_id = 0;

        $fromLast = DB::table('cash_payment_ledger')
            ->where('emp_id', $Cr_emp_id)
            ->where('UserType', 4)
            ->orderByDesc('cash_payment_ledger_id')
            ->first();
        $Open = $fromLast->close ?? 0;
        $Close = $Open + $amount;

        DB::table('cash_payment_ledger')->insert([
            'emp_id' => $Cr_emp_id,
            'invoices_Id' => $invoices_Id,
            'open' => $Open,
            'credit' => $amount,
            'debit' => 0,
            'close' => $Close,
            'credit_emp_id' => $Cr_emp_id,
            'debit_emp_id' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            'UserType'   => 4,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint resolved successfully.');
    }
}
