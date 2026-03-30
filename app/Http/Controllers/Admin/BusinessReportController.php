<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Support\LeadWorkflow;
use Illuminate\Http\Request;

class BusinessReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $leadQuery = Lead::query()->with(['customer', 'createdBy', 'showroom', 'quotations.category', 'quotations.product', 'histories.user', 'payments']);

        if ($fromDate) {
            $leadQuery->whereDate('CreatedDate', '>=', $fromDate);
        }
        if ($toDate) {
            $leadQuery->whereDate('CreatedDate', '<=', $toDate);
        }

        $leads = $leadQuery->get();

        $totalQuotationValue = (float) $leads->sum('iLeadAmount');


        $approvedStatuses = [
            LeadWorkflow::STATUS_QUOTATION_APPROVED,
            LeadWorkflow::STATUS_ADVANCE_RECEIVED,
            LeadWorkflow::STATUS_PRODUCTION_ACCEPTED,
            LeadWorkflow::STATUS_READY_TO_DISPATCHED,
            LeadWorkflow::STATUS_DISPATCHED,
            LeadWorkflow::STATUS_DISPATCHED_DONE,
            LeadWorkflow::STATUS_FITTING_PENDING,
            LeadWorkflow::STATUS_FITTING_DONE,
            LeadWorkflow::STATUS_DEAL_DONE,
        ];

        $approvedBusinessValue = (float) $leads
            ->whereIn('iCurrentLeadStatus', $approvedStatuses)
            ->sum('iLeadAmount');

        $businessDoneStatuses = [
            LeadWorkflow::STATUS_DISPATCHED_DONE,
            LeadWorkflow::STATUS_FITTING_DONE,
            LeadWorkflow::STATUS_DEAL_DONE,
        ];

        $doneBusinessValue = (float) $leads
            ->whereIn('iCurrentLeadStatus', $businessDoneStatuses)
            ->sum('iLeadAmount');

        $paymentQuery = LeadPayment::query()->whereIn('iLeadId', $leads->pluck('iLeadId')->all());
        $receivedAmount = (float) $paymentQuery->sum('iPaidAmount');
        $pendingAmount = max(0, $totalQuotationValue - $receivedAmount);

        $todayBusiness = (float) Lead::whereDate('CreatedDate', now()->toDateString())
            ->where('iCurrentLeadStatus', LeadWorkflow::STATUS_DEAL_DONE)
            ->sum('iLeadAmount');

        $showroomWiseBusiness = Lead::query()
            ->selectRaw('iShowroomId, SUM(iLeadAmount) as total_amount')
            ->where('iCurrentLeadStatus', LeadWorkflow::STATUS_DEAL_DONE)
            ->groupBy('iShowroomId')
            ->with('showroom')
            ->get();

        $salesExecutiveSummary = $leads
            ->groupBy(function ($lead) {
                return $lead->iCustomerId . '|' . $lead->iCreatedBy;
            })
            ->map(function ($group) {
                $first = $group->first();

                $approvedStatuses = [
                    LeadWorkflow::STATUS_QUOTATION_APPROVED,
                    LeadWorkflow::STATUS_ADVANCE_RECEIVED,
                    LeadWorkflow::STATUS_PRODUCTION_ACCEPTED,
                    LeadWorkflow::STATUS_READY_TO_DISPATCHED,
                    LeadWorkflow::STATUS_DISPATCHED,
                    LeadWorkflow::STATUS_DISPATCHED_DONE,
                    LeadWorkflow::STATUS_FITTING_PENDING,
                    LeadWorkflow::STATUS_FITTING_DONE,
                    LeadWorkflow::STATUS_DEAL_DONE,
                ];
                $businessDoneStatuses = [
                    LeadWorkflow::STATUS_DISPATCHED_DONE,
                    LeadWorkflow::STATUS_FITTING_DONE,
                    LeadWorkflow::STATUS_DEAL_DONE,
                ];

                $quotationGivenAmount = (float) $group->sum('iLeadAmount');
                $quotationDoneAmount = (float) $group->whereIn('iCurrentLeadStatus', $businessDoneStatuses)->sum('iLeadAmount');

                $receivedAmount = (float) $group->sum(function ($lead) {
                    return $lead->payments->sum('iPaidAmount');
                });
                $approvedAmount = (float) $group->whereIn('iCurrentLeadStatus', $approvedStatuses)->sum('iLeadAmount');
                $pendingAmount = max(0, $approvedAmount - $receivedAmount);

                return [
                    'customer_name' => optional($first->customer)->strCustomer ?? 'N/A',
                    'sales_executive_name' => optional($first->createdBy)->name ?? 'N/A',
                    'history' => $group,
                    'total_quotation_given' => $quotationGivenAmount,
                    'total_quotation_done' => $quotationDoneAmount,
                    'total_payment_pending' => $pendingAmount,
                    'total_payment_received' => $receivedAmount,
                    'approved_total' => $approvedAmount,
                    'lead_count' => $group->count(),
                ];
            })
            ->values();

        $invoiceItems = $leads->flatMap(function ($lead) {
            return $lead->quotations->map(function ($item) use ($lead) {
                return [
                    'lead_no' => $lead->strLeadNo,
                    'product_category' => optional($item->category)->strCategoryName ?? '-',
                    'product' => optional($item->product)->strProductName ?? '-',
                    'quantity' => $item->quantity ?? 0,
                    'amount' => $item->iAmount ?? 0,
                ];
            });
        });

        return view('admin.reports.business', compact(
            'fromDate',
            'toDate',
            'totalQuotationValue',
            'approvedBusinessValue',
            'doneBusinessValue',
            'receivedAmount',
            'pendingAmount',
            'todayBusiness',
            'showroomWiseBusiness',
            'salesExecutiveSummary',
            'invoiceItems'
        ));
    }
}
