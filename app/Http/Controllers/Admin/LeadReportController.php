<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\LeadQuotation;
use App\Models\User;
use App\Support\LeadWorkflow;
use Illuminate\Http\Request;

class LeadReportController extends Controller
{
    private function authoriseAdmin(): void
    {
        abort_unless(blank(optional(auth()->user()->crmRole)->slug), 403);
    }

    public function index(Request $request)
    {
        $this->authoriseAdmin();

        $query = Lead::query()
            ->with(['customer', 'showroom', 'createdBy.showrooms', 'quotations', 'payments', 'histories'])
            ->withMax('histories as last_history_entry', 'EntryDate')
            ->latest('iLeadId');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('strLeadNo', 'like', "%{$search}%")
                    ->orWhere('iCurrentLeadStatus', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($sub) use ($search) {
                        $sub->where('strCustomer', 'like', "%{$search}%")
                            ->orWhere('strMobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('showroom', function ($sub) use ($search) {
                        $sub->where('strShowRoomName', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('iCurrentLeadStatus', $request->status);
        }

        if ($request->filled('sales_person')) {
            $query->where('iCreatedBy', $request->sales_person);
        }

        if ($request->filled('sales_person_search')) {
            $salesPersonSearch = trim((string) $request->sales_person_search);
            $query->whereHas('createdBy', function ($sub) use ($salesPersonSearch) {
                $sub->where('strUserName', 'like', "%{$salesPersonSearch}%")
                    ->orWhere('name', 'like', "%{$salesPersonSearch}%")
                    ->orWhere('first_name', 'like', "%{$salesPersonSearch}%")
                    ->orWhere('last_name', 'like', "%{$salesPersonSearch}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('CreatedDate', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('CreatedDate', '<=', $request->to_date);
        }

        $filteredLeads = (clone $query)->get();
        $leads = $query->paginate(20)->withQueryString();
        $statusOptions = LeadWorkflow::allStatuses();
        $salesPersons = User::query()
            ->whereIn('id', Lead::query()->whereNotNull('iCreatedBy')->distinct()->pluck('iCreatedBy'))
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'strUserName']);

        $salesPersonWiseData = $filteredLeads
            ->groupBy('iCreatedBy')
            ->map(function ($group) {
                $first = $group->first();
                $salesPerson = optional($first->createdBy);
                $assignedShowrooms = $salesPerson->showrooms
                    ? $salesPerson->showrooms->pluck('strShowRoomName')->filter()->unique()->values()
                    : collect();

                $showroomName = $assignedShowrooms->isNotEmpty()
                    ? $assignedShowrooms->implode(', ')
                    : 'N/A';
                $salesPersonName = $salesPerson->strUserName
                    ?: trim(($salesPerson->first_name ?? '') . ' ' . ($salesPerson->last_name ?? ''))
                    ?: ($salesPerson->name ?? 'Unassigned');

                return [
                    'sales_person_name' => $salesPersonName,
                    'showroom_name' => $showroomName,
                    'quotation_total' => (float) $group->sum('iLeadAmount'),
                    'payment_total' => (float) $group->sum(fn($lead) => (float) $lead->payments->sum('iPaidAmount')),
                ];
            })
            ->sortByDesc('quotation_total')
            ->values();

        return view('admin.reports.leads', compact('leads', 'statusOptions', 'salesPersons', 'salesPersonWiseData'));
    }

    public function show(Lead $lead)
    {
        $this->authoriseAdmin();

        $lead->load(['customer', 'showroom', 'createdBy']);

        $historyCount = $lead->histories()->count();
        $quotationCount = $lead->quotations()->count();
        $paymentCount = $lead->payments()->count();
        $paymentTotal = (float) $lead->payments()->sum('iPaidAmount');

        return view('admin.reports.lead-detail', compact(
            'lead',
            'historyCount',
            'quotationCount',
            'paymentCount',
            'paymentTotal'
        ));
    }

    public function histories(Request $request, Lead $lead)
    {
        $this->authoriseAdmin();

        $histories = $lead->histories()
            ->with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('iStatus', $request->status);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $statusOptions = LeadWorkflow::allStatuses();

        return view('admin.reports.lead-histories', compact('lead', 'histories', 'statusOptions'));
    }

    public function quotations(Request $request, Lead $lead)
    {
        $this->authoriseAdmin();

        $quotations = LeadQuotation::query()
            ->with(['category', 'product', 'shape', 'feature'])
            ->where('iLeadId', $lead->iLeadId)
            ->when($request->filled('batch_id'), function ($query) use ($request) {
                $query->where('quotation_batch_id', $request->batch_id);
            })
            ->orderByDesc('quotation_batch_id')
            ->orderByDesc('iQuotationId')
            ->paginate(20)
            ->withQueryString();

        $batchOptions = LeadQuotation::query()
            ->where('iLeadId', $lead->iLeadId)
            ->whereNotNull('quotation_batch_id')
            ->distinct()
            ->orderByDesc('quotation_batch_id')
            ->pluck('quotation_batch_id');

        return view('admin.reports.lead-quotations', compact('lead', 'quotations', 'batchOptions'));
    }

    public function payments(Request $request, Lead $lead)
    {
        $this->authoriseAdmin();

        $payments = LeadPayment::query()
            ->with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->when($request->filled('payment_mode'), function ($query) use ($request) {
                $query->where('PaymentMode', $request->payment_mode);
            })
            ->orderByDesc('iLeadPaymentId')
            ->paginate(20)
            ->withQueryString();

        $paymentTotal = (float) LeadPayment::query()
            ->where('iLeadId', $lead->iLeadId)
            ->sum('iPaidAmount');

        return view('admin.reports.lead-payments', compact('lead', 'payments', 'paymentTotal'));
    }
}
