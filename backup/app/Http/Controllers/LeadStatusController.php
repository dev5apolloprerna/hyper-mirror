<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\LeadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadStatusController extends Controller
{
    // -------------------------------------------------------
    // Show lead detail page
    // -------------------------------------------------------

    public function show(int $leadId)
    {
        $user     = Auth::user();
        $roleSlug = optional($user->crmRole)->slug;

        $lead = $this->findLeadForRole($leadId, $roleSlug, $user->id);

        if (!$lead) {
            abort(404, 'Lead not found or you do not have access.');
        }

        $nextStatuses   = LeadStatus::nextStatuses($roleSlug, $lead->iCurrentLeadStatus);
        $statusLabels   = LeadStatus::labels();
        $dateConfigsJson = LeadStatus::dateConfigsJson();
        $histories      = $lead->histories()->with('enteredBy')->get();

        return view('leads.show', compact(
            'lead',
            'nextStatuses',
            'statusLabels',
            'dateConfigsJson',
            'histories',
            'roleSlug'
        ));
    }

    // -------------------------------------------------------
    // Update lead status
    // -------------------------------------------------------

    public function updateStatus(Request $request, int $leadId)
    {
        $user     = Auth::user();
        $roleSlug = optional($user->crmRole)->slug;

        $lead = $this->findLeadForRole($leadId, $roleSlug, $user->id);

        if (!$lead) {
            abort(404, 'Lead not found or you do not have access.');
        }

        $allowedNextStatuses = LeadStatus::nextStatuses($roleSlug, $lead->iCurrentLeadStatus);

        if (empty($allowedNextStatuses)) {
            return back()->withErrors(['status' => 'You cannot change this lead\'s status.']);
        }

        $newStatus  = (int) $request->input('new_status');
        $dateConfig = LeadStatus::dateConfig($newStatus);
        $dateRequired = $dateConfig !== null;

        // Build validation rules
        $rules = [
            'new_status'  => ['required', 'integer', 'in:' . implode(',', $allowedNextStatuses)],
            'strComments' => ['required', 'string', 'max:1000'],
            'status_date' => $dateRequired
                ? ['required', 'date', 'after_or_equal:today']
                : ['nullable', 'date'],
        ];

        $messages = [
            'new_status.in'            => 'Invalid status selected.',
            'strComments.required'     => 'A comment is required when changing status.',
            'status_date.required'     => ($dateConfig['label'] ?? 'Date') . ' is required for this status.',
            'status_date.after_or_equal' => ($dateConfig['label'] ?? 'Date') . ' must be today or a future date.',
        ];

        $validated = $request->validate($rules, $messages);

        DB::transaction(function () use ($validated, $lead, $user, $dateConfig, $newStatus) {
            $statusDate = $validated['status_date'] ?? null;

            // Update lead status
            $lead->iCurrentLeadStatus = $newStatus;

            // Save the date into the correct column
            if ($dateConfig && $statusDate) {
                $lead->{$dateConfig['field']} = $statusDate;

                // Also update NetFollowupdate for followup-type dates
                if ($dateConfig['field'] === 'NetFollowupdate') {
                    $lead->NetFollowupdate = $statusDate;
                }
            }

            $lead->save();

            // Write history
            LeadHistory::create([
                'iLeadId'         => $lead->iLeadId,
                'strComments'     => $validated['strComments'],
                'NetFolloupwdate' => $statusDate,
                'iStatus'         => $newStatus,
                'iEnterBy'        => $user->id,
                'EntryDate'       => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        });

        return back()->with('success', 'Lead status updated to "' . LeadStatus::label($newStatus) . '" successfully.');
    }

    // -------------------------------------------------------
    // Leads list per role (used by dashboard controllers)
    // -------------------------------------------------------

    public static function leadsForRole(string $roleSlug, int $userId, ?int $statusFilter = null)
    {
        $query = Lead::with(['customer', 'createdBy', 'showroom']);

        switch ($roleSlug) {
            case 'storemanager':
                $query->ownedBy($userId);
                if ($statusFilter) {
                    $query->byStatus($statusFilter);
                }
                break;

            case 'measurement':
                $query->byStatus(LeadStatus::IN_MEASUREMENT);
                break;

            case 'production':
                $query->whereIn('iCurrentLeadStatus', [
                    LeadStatus::ADVANCE_RECEIVED,
                    LeadStatus::PRODUCTION_PENDING,
                ]);
                break;

            case 'dispatch':
                $query->byStatus(LeadStatus::READY_TO_DISPATCH);
                break;

            case 'fitting':
                $query->byStatus(LeadStatus::DISPATCHED);
                break;

            case 'account':
                $query->whereNotIn('iCurrentLeadStatus', [
                    LeadStatus::IN_MEASUREMENT,
                    LeadStatus::LEAD_REJECTED,
                ]);
                break;

            default:
                $query->whereRaw('1 = 0');
        }

        return $query->orderByDesc('updated_at')->paginate(20);
    }

    // -------------------------------------------------------
    // Private: find lead scoped to role
    // -------------------------------------------------------

    private function findLeadForRole(int $leadId, ?string $roleSlug, int $userId): ?Lead
    {
        $query = Lead::where('iLeadId', $leadId);

        match ($roleSlug) {
            'storemanager' => $query->where('iCreatedBy', $userId),
            'measurement'  => $query->where('iCurrentLeadStatus', LeadStatus::IN_MEASUREMENT),
            'production'   => $query->whereIn('iCurrentLeadStatus', [
                                 LeadStatus::ADVANCE_RECEIVED,
                                 LeadStatus::PRODUCTION_PENDING,
                             ]),
            'dispatch'     => $query->where('iCurrentLeadStatus', LeadStatus::READY_TO_DISPATCH),
            'fitting'      => $query->where('iCurrentLeadStatus', LeadStatus::DISPATCHED),
            'account'      => $query->whereNotIn('iCurrentLeadStatus', [LeadStatus::LEAD_REJECTED]),
            default        => $query->whereRaw('1 = 0'),
        };

        return $query->with(['customer', 'histories.enteredBy', 'showroom'])->first();
    }
}
