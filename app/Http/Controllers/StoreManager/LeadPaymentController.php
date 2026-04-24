<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\LedgerHelper;


class LeadPaymentController extends Controller
{
    private function roleSlug(): ?string
    {
        return optional(auth()->user()->crmRole)->slug;
    }

    private function assertAccountantOnly(): void
    {
        abort_unless($this->roleSlug() === 'account', 403, 'Only accountant can manage payment entries.');
    }


    public function index(Request $request, Lead $lead)
    {
        $query = LeadPayment::with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->orderBy('iLeadPaymentId', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('iPaidAmount', 'like', "%{$search}%")
                    ->orWhere('PaymentDate', 'like', "%{$search}%")
                    ->orWhere('PaymentMode', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')) like ?", ["%{$search}%"])
                            ->orWhere('strUserName', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        $canManagePayments = $this->roleSlug() === 'account';

        return view('store-manager.lead-payments.index', compact('lead', 'payments', 'canManagePayments'));

        // return view('store-manager.lead-payments.index', compact('lead', 'payments'));
    }

    public function store(Request $request, Lead $lead)
    {
        $this->assertAccountantOnly();

        $request->validate([
            'iPaidAmount' => 'required|numeric|min:0.01',
            'iDiscountAmount' => 'nullable|numeric|min:0',
            'PaymentDate' => 'required|date',
            'PaymentMode' => 'required|string|max:30',
        ]);

        DB::beginTransaction();

        try {
            LeadPayment::create([
                'iLeadId' => $lead->iLeadId,
                'iPaidAmount' => $request->iPaidAmount,
                'iDiscountAmount' => $request->iDiscountAmount ?? 0,
                'PaymentDate' => $request->PaymentDate,
                'PaymentMode' => $request->PaymentMode,
                'iUserID' => auth()->id(),
            ]);

            $auth = Auth::user()->id;
            $invoiceIds = $lead->iLeadId ?? 0;
            $paymentmode = strtolower($request->PaymentMode) === 'cash' ? 0 : 1;

            if ($paymentmode == 0) {
                $Cr_emp_id = $auth;
                $invoices_Id  =  $invoiceIds;
                $amount = $request->iPaidAmount;
                $dr_emp_id = 0;

                $Account_get_data = DB::table('cash_payment_ledger')
                    ->where('emp_id', $Cr_emp_id)
                    ->where('UserType', 2)
                    ->orderByDesc('cash_payment_ledger_id')
                    ->first();

                $AccountOpening = $Account_get_data->close ?? 0;
                $Accountcredit = $amount ?? 0;
                $Accountdebit = 0;
                $AccountClose = $AccountOpening + $amount ?? 0;

                DB::table('cash_payment_ledger')->insert([
                    'emp_id' => $Cr_emp_id,
                    'invoices_Id' => $invoices_Id,
                    'open' => $AccountOpening,
                    'credit' => $Accountcredit,
                    'debit' => $Accountdebit,
                    'close' => $AccountClose,
                    'credit_emp_id' => $Cr_emp_id,
                    'debit_emp_id' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'UserType'   => 2,
                ]);
            }

            DB::commit();

            return redirect()->route('store.leads.payments.index', $lead->iLeadId)
                ->with('success', 'Lead payment added successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, Lead $lead, LeadPayment $payment)
    {
        $this->assertAccountantOnly();

        if ($payment->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $request->validate([
            'iPaidAmount' => 'required|numeric|min:0.01',
            'iDiscountAmount' => 'nullable|numeric|min:0',
            'PaymentDate' => 'required|date',
            'PaymentMode' => 'required|string|max:30',
        ]);

        DB::beginTransaction();

        try {
            $payment->update([
                'iPaidAmount' => $request->iPaidAmount,
                'iDiscountAmount' => $request->iDiscountAmount ?? 0,
                'PaymentDate' => $request->PaymentDate,
                'PaymentMode' => $request->PaymentMode,
            ]);

            DB::commit();

            return redirect()->route('store.leads.payments.index', $lead->iLeadId)
                ->with('success', 'Lead payment updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function destroy(Lead $lead, LeadPayment $payment)
    {

        $this->assertAccountantOnly();
        if ($payment->iLeadId != $lead->iLeadId) {
            abort(404);
        }
        $auth = Auth::user()->id;
        $amount = $payment->iPaidAmount;
        $iLeadId = $payment->iLeadId;
        $Cr_emp_id = $auth;
        $dr_emp_id = 0;

        $Get_data = DB::table('cash_payment_ledger')
            ->where('emp_id', $Cr_emp_id)
            ->where('UserType', 2)
            ->orderByDesc('cash_payment_ledger_id')
            ->first();

        $Open = $Get_data->close ?? 0;
        $credit = 0;
        $debit = $amount;
        $Close = $Open - $amount;

        DB::table('cash_payment_ledger')->insert([
            'emp_id' => $Cr_emp_id,
            'invoices_Id' => $iLeadId,
            'open' => $Open,
            'credit' => $credit,
            'debit' => $debit,
            'close' => $Close,
            'credit_emp_id' => 0,
            'debit_emp_id' => $Cr_emp_id,
            'UserType' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payment->delete();

        return redirect()->route('store.leads.payments.index', $lead->iLeadId)
            ->with('success', 'Lead payment deleted successfully.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
        $this->assertAccountantOnly();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:lead_payments,iLeadPaymentId',
        ]);

        LeadPayment::where('iLeadId', $lead->iLeadId)
            ->whereIn('iLeadPaymentId', $request->ids)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Selected lead payments deleted successfully.'
        ]);
    }
    public function pdf(Lead $lead)
    {
        $payments = LeadPayment::with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->orderBy('PaymentDate')
            ->orderBy('iLeadPaymentId')
            ->get();

        $totalPaid = (float) $payments->sum('iPaidAmount');
        $totalDiscount = (float) $payments->sum('iDiscountAmount');
        $leadAmount = (float) ($lead->iLeadAmount ?? 0);
        $pendingAmount = max(0, $leadAmount - ($totalPaid + $totalDiscount));

        $pdf = Pdf::loadView('store-manager.lead-payments.payment-pdf', compact(
            'lead',
            'payments',
            'totalPaid',
            'totalDiscount',
            'leadAmount',
            'pendingAmount'
        ));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('payment-summary-' . $lead->strLeadNo . '.pdf');
    }
}
