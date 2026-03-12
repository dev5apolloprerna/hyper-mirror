<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadPaymentController extends Controller
{
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
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('store-manager.lead-payments.index', compact('lead', 'payments'));
    }

    public function store(Request $request, Lead $lead)
    {
        $request->validate([
            'iPaidAmount' => 'required|numeric|min:0.01',
            'PaymentDate' => 'required|date',
            'PaymentMode' => 'required|string|max:30',
        ]);

        DB::beginTransaction();

        try {
            LeadPayment::create([
                'iLeadId' => $lead->iLeadId,
                'iPaidAmount' => $request->iPaidAmount,
                'PaymentDate' => $request->PaymentDate,
                'PaymentMode' => $request->PaymentMode,
                'iUserID' => auth()->id(),
            ]);

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
        if ($payment->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $request->validate([
            'iPaidAmount' => 'required|numeric|min:0.01',
            'PaymentDate' => 'required|date',
            'PaymentMode' => 'required|string|max:30',
        ]);

        DB::beginTransaction();

        try {
            $payment->update([
                'iPaidAmount' => $request->iPaidAmount,
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
        if ($payment->iLeadId != $lead->iLeadId) {
            abort(404);
        }

        $payment->delete();

        return redirect()->route('store.leads.payments.index', $lead->iLeadId)
            ->with('success', 'Lead payment deleted successfully.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
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
}