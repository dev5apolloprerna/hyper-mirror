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

        $leadQuery = Lead::query()->with(['customer', 'createdBy', 'showroom', 'quotations.category', 'quotations.product', 'histories.user']);

        if ($fromDate) {
            $leadQuery->whereDate('CreatedDate', '>=', $fromDate);
        }
        if ($toDate) {
            $leadQuery->whereDate('CreatedDate', '<=', $toDate);
        }

        $leads = $leadQuery->get();

        $totalQuotationValue = (float) $leads->sum('iLeadAmount');
        $approvedBusinessValue = (float) $leads
            ->where('iCurrentLeadStatus', LeadWorkflow::STATUS_QUOTATION_APPROVED)
            ->sum('iLeadAmount');
        $doneBusinessValue = (float) $leads
            ->where('iCurrentLeadStatus', LeadWorkflow::STATUS_QUOTATION_APPROVED)
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
            ->groupBy('iCreatedBy')
            ->map(function ($group) {
                $first = $group->first();
                $total = $group->count();
                $done = $group->where('iCurrentLeadStatus', LeadWorkflow::STATUS_DEAL_DONE)->count();
                $pending = $total - $done;

                return [
                    'customer_names' => $group->pluck('customer.strCustomer')->filter()->unique()->values(),
                    'sales_executive_name' => optional($first->createdBy)->name ?? 'N/A',
                    'total_quotations' => $total,
                    'done_count' => $done,
                    'pending_count' => $pending,
                    'history' => $group,
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
