<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use App\Support\LeadWorkflow;

class PartyReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $leadQuery = Lead::query()->with(['customer', 'createdBy', 'showroom', 'payments']);

        if ($fromDate) {
            $leadQuery->whereDate('CreatedDate', '>=', $fromDate);
        }

        if ($toDate) {
            $leadQuery->whereDate('CreatedDate', '<=', $toDate);
        }

        $leads = $leadQuery->get();

        $approvedStatuses = [
            LeadWorkflow::STATUS_QUOTATION_APPROVED,
            LeadWorkflow::STATUS_ADVANCE_RECEIVED,
            LeadWorkflow::STATUS_PRODUCTION_ACCEPTED,
            LeadWorkflow::STATUS_READY_TO_DISPATCHED,
            LeadWorkflow::STATUS_DISPATCHED,
            LeadWorkflow::STATUS_RECEIVED_AT_NAROL,
            LeadWorkflow::STATUS_DISPATCHED_DONE,
            LeadWorkflow::STATUS_FITTING_PENDING,
            LeadWorkflow::STATUS_FITTING_DONE,
            LeadWorkflow::STATUS_DEAL_DONE,
        ];


        $partySummary = $leads
            ->groupBy('iCustomerId')
            ->map(function ($group) use ($approvedStatuses) {
                $first = $group->first();

                $totalAmount = (float) $group->sum('iLeadAmount');
                $paidAmount = (float) $group->sum(function ($lead) {
                    return $lead->payments->sum('iPaidAmount');
                });
                $unpaidAmount = max(0, $totalAmount - $paidAmount);
                $approvedLeads = $group->whereIn('iCurrentLeadStatus', $approvedStatuses);
                $approvedAmount = (float) $approvedLeads->sum('iLeadAmount');
                $paymentEntries = (int) $group->sum(function ($lead) {
                    return $lead->payments->count();
                });
                $lastPaymentDate = $group
                    ->flatMap(fn ($lead) => $lead->payments)
                    ->pluck('PaymentDate')
                    ->filter()
                    ->sortDesc()
                    ->first();

                return [
                    'party_name' => optional($first->customer)->strCustomer ?? 'N/A',
                    'mobile' => optional($first->customer)->strMobile ?? 'N/A',
                    'total_amount' => $totalAmount,
                     'approved_amount' => $approvedAmount,
                    'approved_lead_count' => $approvedLeads->count(),
                    'paid_amount' => $paidAmount,
                    'unpaid_amount' => $unpaidAmount,
                    'lead_count' => $group->count(),
                    'payment_entry_count' => $paymentEntries,
                    'last_payment_date' => $lastPaymentDate,
                    'leads' => $group->sortByDesc(function ($lead) {
                        return $lead->CreatedDate ?? $lead->created_at;
                    })->values(),
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        return view('admin.reports.party', compact('fromDate', 'toDate', 'partySummary'));
    }
}
