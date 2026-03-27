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
    public const STATUS_DISPATCHED_DONE     = 'Dispatched Done';
    public const STATUS_FITTING_PENDING     = 'Fitting Pending';
    public const STATUS_FITTING_DONE        = 'Fitting Done';

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
            self::STATUS_DISPATCHED,
            self::STATUS_DISPATCHED_DONE,
            self::STATUS_FITTING_PENDING,
            self::STATUS_FITTING_DONE,
        ];
    }

    // ── Statuses that REQUIRE a follow-up date ──────────────────────────────
    public static function followupRequiredStatuses(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT,
            self::STATUS_IN_DESIGN,
            self::STATUS_QUOTATION_SENT,
            self::STATUS_QUOTATION_APPROVED,
            self::STATUS_ADVANCE_RECEIVED,
            self::STATUS_PRODUCTION_ACCEPTED,
            self::STATUS_READY_TO_DISPATCHED,
            self::STATUS_DISPATCHED,
            self::STATUS_FITTING_PENDING,
        ];
    }

    // ── Which statuses appear in each role's queue/list ─────────────────────
    public static function roleQueueStatuses(?string $roleSlug): array
    {
        return match ($roleSlug) {
            'measurement' => [
                self::STATUS_IN_MEASUREMENT,
            ],
            'production' => [
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_PRODUCTION_ACCEPTED,
                self::STATUS_READY_TO_DISPATCHED,
            ],
            'dispatch' => [
                self::STATUS_READY_TO_DISPATCHED,
                self::STATUS_DISPATCHED,
                self::STATUS_DISPATCHED_DONE,
            ],
            'fitting' => [
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
            ],
            'account' => [
                self::STATUS_IN_DESIGN,
                self::STATUS_QUOTATION_SENT,
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_LEAD_REJECTED,
                self::STATUS_FITTING_DONE,
            ],
            default => self::allStatuses(), // storemanager sees all
        };
    }

    // ── Full workflow transition map (from → allowed tos) ───────────────────
    // This is the global pipeline — roles further restrict via allowedTransitionsFor()
    public static function transitionMap(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT      => [self::STATUS_MEASUREMENT_DONE],
            self::STATUS_MEASUREMENT_DONE    => [self::STATUS_IN_DESIGN, self::STATUS_QUOTATION_SENT],
            self::STATUS_IN_DESIGN           => [self::STATUS_QUOTATION_SENT, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_SENT      => [self::STATUS_QUOTATION_APPROVED, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_APPROVED  => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_LEAD_REJECTED],
            self::STATUS_ADVANCE_RECEIVED    => [self::STATUS_PRODUCTION_ACCEPTED],
            self::STATUS_PRODUCTION_ACCEPTED => [self::STATUS_READY_TO_DISPATCHED],
            self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED],
            self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE],
            self::STATUS_DISPATCHED_DONE     => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_PENDING     => [self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_DONE        => [],
            self::STATUS_LEAD_REJECTED       => [],
        ];
    }

    // ── Per-role transition overrides ────────────────────────────────────────
    // What each role is ALLOWED to move to from the current status
    protected static function roleTransitionMap(): array
    {
        return [
            // Measurement staff: can only move In Measurement → Measurement Done
            'measurement' => [
                self::STATUS_IN_MEASUREMENT => [self::STATUS_MEASUREMENT_DONE],
            ],

            // Store manager controls Measurement Done → next + all sales funnel steps
            'storemanager' => [],  // handled separately — gets all transitions

            // Production: accepts Advance Received, moves through to Ready to Dispatch
            'production' => [
                self::STATUS_ADVANCE_RECEIVED    => [self::STATUS_PRODUCTION_ACCEPTED],
                self::STATUS_PRODUCTION_ACCEPTED => [self::STATUS_READY_TO_DISPATCHED],
            ],

            // Dispatch: marks dispatched
            'dispatch' => [
                self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED],
                self::STATUS_DISPATCHED          => [self::STATUS_DISPATCHED_DONE],
            ],

            // Fitting: marks fitting done
            'fitting' => [
                self::STATUS_DISPATCHED_DONE => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
                self::STATUS_FITTING_PENDING => [self::STATUS_FITTING_DONE],
            ],

            // Account: can only add remarks/payments — no status change
            'account' => [],
        ];
    }

    /**
     * Returns the statuses the given user is allowed to transition the lead TO.
     * Returns empty array if the user has no permission to change status.
     */
    public static function allowedTransitionsFor(User $user, Lead $lead): array
    {
        $roleSlug = optional($user->crmRole)->slug;

        // Store manager can go anywhere in the global transition map
        if ($roleSlug === 'storemanager') {
            return self::transitionMap()[$lead->iCurrentLeadStatus] ?? [];
        }

        $roleMap = self::roleTransitionMap()[$roleSlug] ?? [];

        return $roleMap[$lead->iCurrentLeadStatus] ?? [];
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

        return in_array($lead->iCurrentLeadStatus, self::roleQueueStatuses($roleSlug), true);
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
                self::STATUS_DISPATCHED,
                self::STATUS_DISPATCHED_DONE,
            ],
            'fitting' => [
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
            ],
            'account' => [
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_FITTING_DONE,
            ],
            default => [
                // Store manager dashboard shows full funnel overview
                self::STATUS_IN_MEASUREMENT,
                self::STATUS_MEASUREMENT_DONE,
                self::STATUS_IN_DESIGN,
                self::STATUS_QUOTATION_SENT,
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
                self::STATUS_PRODUCTION_ACCEPTED,
                self::STATUS_READY_TO_DISPATCHED,
                self::STATUS_DISPATCHED,
                self::STATUS_DISPATCHED_DONE,
                self::STATUS_FITTING_PENDING,
                self::STATUS_FITTING_DONE,
            ],
        };
    }

    // ── Fitting collection helper ────────────────────────────────────────────
    public static function fittingCollectedThisMonth(): array
    {
        return [self::STATUS_FITTING_DONE];
    }
}
