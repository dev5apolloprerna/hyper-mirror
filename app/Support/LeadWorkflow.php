<?php

namespace App\Support;

use App\Models\Lead;
use App\Models\User;

class LeadWorkflow
{
    // ── Status constants ────────────────────────────────────────────────────
    public const STATUS_IN_MEASUREMENT      = 'In Measurement';
    public const STATUS_MEASUREMENT_DONE    = 'Measurement Done';
    public const STATUS_IN_DESIGN           = 'In Design';
    public const STATUS_QUOTATION_SENT      = 'Quotation Sent';
    public const STATUS_LEAD_REJECTED       = 'Lead Rejected';
    public const STATUS_QUOTATION_APPROVED  = 'Quotation Approved';
    public const STATUS_ADVANCE_RECEIVED    = 'Advance Received';
    public const STATUS_PRODUCTION_ACCEPTED = 'Production Accepted';
    public const STATUS_READY_TO_DISPATCHED = 'Ready to Dispatched';
    public const STATUS_DISPATCHED          = 'Dispatched';
    public const STATUS_RECEIVED_AT_NAROL   = 'Received @ Narol';
    public const STATUS_DISPATCHED_DONE     = 'Dispatched Done';
    public const STATUS_FITTING_PENDING     = 'Fitting Pending';
    public const STATUS_FITTING_DONE        = 'Fitting Done';
    public const STATUS_DEAL_DONE           = 'Deal Done';

    // ── All statuses in workflow order ──────────────────────────────────────
    public static function allStatuses(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT,
            self::STATUS_MEASUREMENT_DONE,
            self::STATUS_IN_DESIGN,
            self::STATUS_QUOTATION_SENT,
            self::STATUS_LEAD_REJECTED,
            self::STATUS_QUOTATION_APPROVED,
            self::STATUS_ADVANCE_RECEIVED,
            self::STATUS_PRODUCTION_ACCEPTED,
            self::STATUS_READY_TO_DISPATCHED,
            //self::STATUS_DISPATCHED,
            self::STATUS_RECEIVED_AT_NAROL,
            self::STATUS_DISPATCHED_DONE,
            self::STATUS_FITTING_PENDING,
            self::STATUS_FITTING_DONE,
            self::STATUS_DEAL_DONE,
        ];
    }

    /**
     * Statuses that require a follow-up date.
     * Note: Lead Rejected & Measurement Done do NOT require a follow-up date.
     */
    public static function followupRequiredStatuses(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT,
            self::STATUS_IN_DESIGN,
            self::STATUS_QUOTATION_SENT,
            self::STATUS_QUOTATION_APPROVED,
            //self::STATUS_ADVANCE_RECEIVED,
           // self::STATUS_PRODUCTION_ACCEPTED,
           // self::STATUS_READY_TO_DISPATCHED,
            //self::STATUS_DISPATCHED,
            self::STATUS_FITTING_PENDING,
        ];
    }

    /**
     * Statuses where the lead is considered "terminal" (no further movement).
     */
    public static function terminalStatuses(): array
    {
        return [
            self::STATUS_LEAD_REJECTED,
            self::STATUS_DEAL_DONE,
        ];
    }

    /**
     * Statuses that are "read-only" for the assigned role —
     * the role can VIEW the lead but not change status further.
     */
        public static function readOnlyForRole(?string $roleSlug, string $currentStatus): bool
    {
        // Production cannot change once "Ready to Dispatched"
        if ($roleSlug === 'production' && $currentStatus === self::STATUS_READY_TO_DISPATCHED) {
            return true;
        }

        return false;
    }

    // ── Which statuses appear in each role's queue/list ─────────────────────
    public static function roleQueueStatuses(?string $roleSlug): array
    {
        return match ($roleSlug) {
            'measurement' => [
                self::STATUS_IN_MEASUREMENT,
                self::STATUS_MEASUREMENT_DONE,
            ],
            'production' => [
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_PRODUCTION_ACCEPTED,
                self::STATUS_READY_TO_DISPATCHED,
            ],
            'dispatch' => [
                self::STATUS_READY_TO_DISPATCHED,
               // self::STATUS_DISPATCHED,
                self::STATUS_RECEIVED_AT_NAROL,
                self::STATUS_DISPATCHED_DONE,
            ],
            'fitting' => [
                self::STATUS_QUOTATION_APPROVED,
                //self::STATUS_DISPATCHED,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
            ],
            'account' => [
                self::STATUS_IN_DESIGN,
                self::STATUS_QUOTATION_SENT,
                self::STATUS_RECEIVED_AT_NAROL,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_LEAD_REJECTED,
                self::STATUS_FITTING_DONE,
                self::STATUS_DEAL_DONE,
            ],
            default => self::allStatuses(), // storemanager sees all
        };
    }

    // ── Full workflow transition map (from → allowed tos) ───────────────────
    public static function transitionMap(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT      => [self::STATUS_MEASUREMENT_DONE],
            self::STATUS_MEASUREMENT_DONE    => [self::STATUS_IN_DESIGN, self::STATUS_QUOTATION_SENT, self::STATUS_LEAD_REJECTED],
            self::STATUS_IN_DESIGN           => [self::STATUS_QUOTATION_SENT, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_SENT      => [self::STATUS_QUOTATION_APPROVED, self::STATUS_LEAD_REJECTED],
            //self::STATUS_QUOTATION_APPROVED  => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_APPROVED  => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_FITTING_PENDING],
            self::STATUS_ADVANCE_RECEIVED    => [self::STATUS_PRODUCTION_ACCEPTED],
            self::STATUS_PRODUCTION_ACCEPTED => [self::STATUS_READY_TO_DISPATCHED],
            /*self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED],
            // self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE],
            self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE, self::STATUS_RECEIVED_AT_NAROL],*/
            self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED_DONE, self::STATUS_RECEIVED_AT_NAROL],
            self::STATUS_RECEIVED_AT_NAROL   => [self::STATUS_DISPATCHED_DONE],
            self::STATUS_DISPATCHED_DONE     => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_PENDING     => [self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_DONE        => [self::STATUS_DEAL_DONE],
            self::STATUS_LEAD_REJECTED       => [],
            self::STATUS_DEAL_DONE           => [],
        ];
    }

    // ── Per-role transition overrides ────────────────────────────────────────
    protected static function roleTransitionMap(): array
    {
        return [
            'measurement' => [
                self::STATUS_IN_MEASUREMENT => [self::STATUS_MEASUREMENT_DONE],
                // Measurement Done: no action needed — storemanager handles next step
            ],
            'storemanager' => [],

            'production' => [
                self::STATUS_ADVANCE_RECEIVED    => [self::STATUS_PRODUCTION_ACCEPTED],
                self::STATUS_PRODUCTION_ACCEPTED => [self::STATUS_READY_TO_DISPATCHED],
                // Ready to Dispatched: read-only for production
            ],

            'dispatch' => [
               /* self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED],
                //self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE],
                self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE, self::STATUS_RECEIVED_AT_NAROL],*/
                self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED_DONE, self::STATUS_RECEIVED_AT_NAROL],
                self::STATUS_RECEIVED_AT_NAROL   => [self::STATUS_DISPATCHED_DONE],
                // Dispatched Done: handled by fitting
            ],

            'fitting' => [
                self::STATUS_QUOTATION_APPROVED => [self::STATUS_FITTING_PENDING],
                //self::STATUS_DISPATCHED => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
                self::STATUS_DISPATCHED_DONE => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
                self::STATUS_FITTING_PENDING => [self::STATUS_FITTING_DONE],
            ],

            'account' => [
                self::STATUS_QUOTATION_APPROVED => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_FITTING_PENDING],
                self::STATUS_FITTING_DONE => [self::STATUS_DEAL_DONE],
            ],
        ];
    }

    /**
     * Returns the statuses the given user is allowed to transition the lead TO.
     */
    public static function allowedTransitionsFor(User $user, Lead $lead): array
    {
        $roleSlug = optional($user->crmRole)->slug;

        // Read-only check
        if (self::readOnlyForRole($roleSlug, $lead->iCurrentLeadStatus)) {
            return [];
        }

        // Store manager can go anywhere in the global transition map
        if ($roleSlug === 'storemanager') {
            $transitions = self::transitionMap()[$lead->iCurrentLeadStatus] ?? [];
         return self::applyFittingRequirementRules($lead, $transitions, $lead->iCurrentLeadStatus);
        }

        $roleMap = self::roleTransitionMap()[$roleSlug] ?? [];
        $transitions = $roleMap[$lead->iCurrentLeadStatus] ?? [];
        
        return self::applyFittingRequirementRules($lead, $transitions, $lead->iCurrentLeadStatus);
    }

protected static function applyFittingRequirementRules(Lead $lead, array $transitions, string $currentStatus): array
    {
        if ((int) ($lead->isFittingRequired ?? 0) === 1) {
             if ($currentStatus === self::STATUS_QUOTATION_APPROVED) {
                return array_values(array_filter(
                    $transitions,
                    fn (string $status) => $status !== self::STATUS_ADVANCE_RECEIVED
                ));
            }
            return $transitions;
        }

        $filtered = array_values(array_filter(
            $transitions,
            fn (string $status) => !in_array($status, [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE], true)
        ));
        if (
            $currentStatus === self::STATUS_DISPATCHED_DONE
            && in_array(self::STATUS_DEAL_DONE, self::transitionMap()[self::STATUS_FITTING_DONE] ?? [], true)
            && !in_array(self::STATUS_DEAL_DONE, $filtered, true)
        ) {
            $filtered[] = self::STATUS_DEAL_DONE;
        }

        return $filtered;
    }

    /**
     * Can the given user SEE / ACCESS this lead at all?
     */
    public static function canAccessLead(User $user, Lead $lead): bool
    {
        $roleSlug = optional($user->crmRole)->slug;

        if ($roleSlug === 'storemanager') {
            return true;
        }
        if ($roleSlug === 'account'
            && $lead->iCurrentLeadStatus === self::STATUS_DISPATCHED_DONE
            && (int) ($lead->isFittingRequired ?? 0) === 0) {
            return true;
        }

        return in_array($lead->iCurrentLeadStatus, self::roleQueueStatuses($roleSlug), true);
    }

 public static function canEditLeadDetails(string $currentStatus): bool
    {
        return in_array($currentStatus, [
            self::STATUS_IN_MEASUREMENT,
            self::STATUS_MEASUREMENT_DONE,
            self::STATUS_IN_DESIGN,
            self::STATUS_QUOTATION_SENT,
        ], true);
    }


    // ── Dashboard status cards per role ─────────────────────────────────────
    public static function dashboardStatuses(?string $roleSlug): array
    {
        return match ($roleSlug) {
            'measurement' => [
                self::STATUS_IN_MEASUREMENT,
                self::STATUS_MEASUREMENT_DONE,
            ],
            'production' => [
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_PRODUCTION_ACCEPTED,
                self::STATUS_READY_TO_DISPATCHED,
            ],
            'dispatch' => [
                self::STATUS_READY_TO_DISPATCHED,
               // self::STATUS_DISPATCHED,
                self::STATUS_RECEIVED_AT_NAROL,
                self::STATUS_DISPATCHED_DONE,
            ],
            'fitting' => [
               // self::STATUS_DISPATCHED,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
            ],
            'account' => [
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_DONE,
                self::STATUS_DEAL_DONE,
            ],
            default => [
                self::STATUS_IN_MEASUREMENT,
                self::STATUS_MEASUREMENT_DONE,
                self::STATUS_IN_DESIGN,
                self::STATUS_QUOTATION_SENT,
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_PRODUCTION_ACCEPTED,
                self::STATUS_READY_TO_DISPATCHED,
               // self::STATUS_DISPATCHED,
                self::STATUS_RECEIVED_AT_NAROL,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
                self::STATUS_DEAL_DONE,
            ],
        };
    }

    public static function fittingCollectedThisMonth(): array
    {
        return [self::STATUS_FITTING_DONE];
    }
}
