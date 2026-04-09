<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Support\LeadWorkflow;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Showroom;

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

        $todayInvoiceCollections = Invoice::query()
            ->with('items')
            ->whereDate('InvoiceDate', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where('payment_received', true)
            ->get()
            ->groupBy('payment_mode');

        $todayCashAmount = (float) optional($todayInvoiceCollections->get('cash'))->sum(function ($invoice) {
            return (float) $invoice->total_amount;
        });
        $todayBankAmount = (float) optional($todayInvoiceCollections->get('bank'))->sum(function ($invoice) {
            return (float) $invoice->total_amount;
        });
        $todayTotalAmount = $todayCashAmount + $todayBankAmount;



        $showroomWiseLeads = Lead::query()
            ->selectRaw('iShowroomId, SUM(iLeadAmount) as quotation_total')
            ->whereIn('iCurrentLeadStatus', $businessDoneStatuses)
            ->groupBy('iShowroomId')
            ->get()
            ->keyBy('iShowroomId');

             $showroomWiseLeadReceived = LeadPayment::query()
            ->join('leads', 'leads.iLeadId', '=', 'lead_payments.iLeadId')
            ->selectRaw('leads.iShowroomId, SUM(lead_payments.iPaidAmount) as quotation_received')
            ->when($fromDate, fn ($query) => $query->whereDate('leads.CreatedDate', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('leads.CreatedDate', '<=', $toDate))
            ->whereIn('leads.iCurrentLeadStatus', $businessDoneStatuses)
            ->groupBy('leads.iShowroomId')
            ->get()
            ->keyBy('iShowroomId');

         $invoiceQuery = Invoice::query()
            ->with(['showroom', 'createdBy', 'items.category', 'items.product'])
            ->where('status', '!=', 'cancelled');

        if ($fromDate) {
            $invoiceQuery->whereDate('InvoiceDate', '>=', $fromDate);
        }
        if ($toDate) {
            $invoiceQuery->whereDate('InvoiceDate', '<=', $toDate);
        }

        $invoices = $invoiceQuery->get();

        $showroomWiseBusiness = $invoices->flatMap(function ($invoice) {
            return $invoice->items->map(function ($item) use ($invoice) {
                return [
                    'branch_name' => optional($invoice->showroom)->strShowRoomName ?? 'N/A',
                    'sales_manager_name' => optional($invoice->createdBy)->first_name ?? 'N/A',
                    'category' => optional($item->category)->strCategoryName ?? '-',
                    'product' => optional($item->product)->strProductName ?? '-',
                    'quantity' => (float) ($item->quantity ?? 0),
                    'amount' => (float) ($item->iAmount ?? 0),
                ];
            });
        })->values();


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
            'todayCashAmount',
            'todayBankAmount',
            'todayTotalAmount',
            'showroomWiseBusiness',
            'salesExecutiveSummary',
            'invoiceItems'
        ));
    }
}
