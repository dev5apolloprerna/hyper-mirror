<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\LeadPayment;
use App\Support\LeadWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Lead $lead)
    {
        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
            optional(auth()->user()->crmRole)->slug === 'storemanager',
            403
        );

        $histories = LeadHistory::with('user')
            ->where('iLeadId', $lead->iLeadId)
            ->orderBy('id', 'desc')
            ->paginate(15);

        $roleSlug = optional(auth()->user()->crmRole)->slug;

        // Determine allowed statuses for the status-change form
        $allowedStatuses = LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);

        $lead->load(['customer', 'quotation']);

        // For "Deal Done": check if payments match quotation amount
        $canCloseDeal = false;
        if (in_array(LeadWorkflow::STATUS_DEAL_DONE, $allowedStatuses)) {
            $totalPaid    = $lead->payments()->sum('iPaidAmount');
            $leadAmount   = (float) $lead->iLeadAmount;
            $canCloseDeal = $leadAmount > 0 && abs($totalPaid - $leadAmount) < 0.01;
            // Remove Deal Done from allowed if payment mismatch
            if (!$canCloseDeal) {
                $allowedStatuses = array_values(array_filter(
                    $allowedStatuses,
                    fn($s) => $s !== LeadWorkflow::STATUS_DEAL_DONE
                ));
            }
        }

        $isReadOnly = LeadWorkflow::readOnlyForRole($roleSlug, $lead->iCurrentLeadStatus)
            || in_array($lead->iCurrentLeadStatus, LeadWorkflow::terminalStatuses());

        return view('store-manager.lead-histories.index', compact(
            'lead',
            'histories',
            'allowedStatuses',
            'roleSlug',
            'isReadOnly',
            'canCloseDeal'
        ));
    }

    public function store(Request $request, Lead $lead)
    {
        abort_unless(
            LeadWorkflow::canAccessLead(auth()->user(), $lead) ||
            optional(auth()->user()->crmRole)->slug === 'storemanager',
            403
        );

        // Prevent changes to terminal statuses
        if (in_array($lead->iCurrentLeadStatus, LeadWorkflow::terminalStatuses())) {
            return back()->with('error', 'This lead is closed and cannot be updated.');
        }

        $isRejection = $request->iStatus === LeadWorkflow::STATUS_LEAD_REJECTED;
        $isDealDone  = $request->iStatus === LeadWorkflow::STATUS_DEAL_DONE;
        $isQuotationApproved = $request->iStatus === LeadWorkflow::STATUS_QUOTATION_APPROVED;

        $rules = [
            'iStatus'        => 'required|string|in:' . implode(',', LeadWorkflow::allStatuses()),
            'strComments'    => 'required|string|max:2000',
        ];

        // Follow-up date: required only for certain statuses, NOT for rejection or measurement done
        if (!$isRejection && !$isDealDone && $request->filled('iStatus')) {
            $needsFollowup = in_array($request->iStatus, LeadWorkflow::followupRequiredStatuses(), true);
            if ($needsFollowup) {
                $rules['NetFolloupwdate'] = 'required|date';
            } else {
                $rules['NetFolloupwdate'] = 'nullable|date';
            }
        } else {
            $rules['NetFolloupwdate'] = 'nullable|date';
        }

        // Rejection reason required
        if ($isRejection) {
            $rules['rejection_reason'] = 'required|string|max:500';
        }

         if ($isQuotationApproved) {
            $rules['expected_delivery_date'] = 'required|date';
        }

        $request->validate($rules);

        $roleSlug = optional(auth()->user()->crmRole)->slug;

        // Determine which statuses this user can set
        $allowedStatuses = $roleSlug === 'storemanager'
            ? (LeadWorkflow::transitionMap()[$lead->iCurrentLeadStatus] ?? [])
            : LeadWorkflow::allowedTransitionsFor(auth()->user(), $lead);

        if (!in_array($request->iStatus, $allowedStatuses, true)) {
            return back()->withInput()->with('error', 'Selected status is not allowed for your role.');
        }

        // "Deal Done" payment check
        if ($isDealDone) {
            $totalPaid  = $lead->payments()->sum('iPaidAmount');
            $leadAmount = (float) $lead->iLeadAmount;
            if ($leadAmount <= 0 || abs($totalPaid - $leadAmount) >= 0.01) {
                return back()->withInput()->with('error',
                    'Cannot mark as Deal Done. Total payments (₹' . number_format($totalPaid, 2) .
                    ') must equal the lead amount (₹' . number_format($leadAmount, 2) . ').'
                );
            }
        }

        DB::beginTransaction();

        try {
            $comments = $request->strComments;

            // Append rejection reason to comments if rejection
            if ($isRejection && $request->filled('rejection_reason')) {
                $comments = 'Rejection Reason: ' . $request->rejection_reason . "\n" . $comments;
            }

            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
             //   'strComments'     => $comments,
                'strComments'     => $isQuotationApproved
                    ? $comments . "\nExpected Delivery Date: " . $request->expected_delivery_date
                    : $comments,
                'NetFolloupwdate' => $isRejection ? null : $request->NetFolloupwdate,
                'iStatus'         => $request->iStatus,
                'iEnterBy'        => auth()->id(),
                'EntryDate'       => now(),
            ]);

            $lead->update([
                'iCurrentLeadStatus' => $request->iStatus,
                'NetFollowupdate'    => $isRejection ? null : $request->NetFolloupwdate,
                'expected_delivery_date' => $isQuotationApproved ? $request->expected_delivery_date : $lead->expected_delivery_date,
            ]);

            DB::commit();

            return redirect()->route('store.leads.histories.index', $lead->iLeadId)
                ->with('success', 'Lead updated to "' . $request->iStatus . '" successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, Lead $lead, LeadHistory $history)
    {
        abort(403, 'History records cannot be edited.');
    }

    public function destroy(Lead $lead, LeadHistory $history)
    {
        abort(403, 'History records cannot be deleted.');
    }

    public function bulkDelete(Request $request, Lead $lead)
    {
        abort(403, 'History records cannot be deleted.');
    }
}
