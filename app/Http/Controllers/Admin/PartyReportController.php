<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

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

        $partySummary = $leads
            ->groupBy('iCustomerId')
            ->map(function ($group) {
                $first = $group->first();

                $totalAmount = (float) $group->sum('iLeadAmount');
                $paidAmount = (float) $group->sum(function ($lead) {
                    return $lead->payments->sum('iPaidAmount');
                });
                $unpaidAmount = max(0, $totalAmount - $paidAmount);

                return [
                    'party_name' => optional($first->customer)->strCustomer ?? 'N/A',
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'unpaid_amount' => $unpaidAmount,
                    'lead_count' => $group->count(),
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
