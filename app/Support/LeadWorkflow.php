<?php

namespace App\Support;

use App\Models\Lead;
use App\Models\User;

class LeadWorkflow
{
    public const STATUS_IN_MEASUREMENT = 'In Measurement';
    public const STATUS_MEASUREMENT_DONE = 'Measurement Done';
    public const STATUS_IN_DESIGN = 'In Design';
    public const STATUS_QUOTATION_SENT = 'Quotation Sent';
    public const STATUS_LEAD_REJECTED = 'Lead Rejected';
    public const STATUS_QUOTATION_APPROVED = 'Quotation Approved';
    public const STATUS_ADVANCE_RECEIVED = 'Advance Received';
    public const STATUS_PRODUCTION_ACCEPTED = 'Production Accepted';
    public const STATUS_READY_TO_DISPATCHED = 'Ready to Dispatched';
    public const STATUS_DISPATCHED = 'Dispatched';
    public const STATUS_DISPATCHED_DONE = 'Dispatched Done';
    public const STATUS_FITTING_PENDING = 'Fitting Pending';
    public const STATUS_FITTING_DONE = 'Fitting Done';

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

    public static function roleQueueStatuses(?string $roleSlug): array
    {
        return match ($roleSlug) {
            'measurement' => [self::STATUS_IN_MEASUREMENT],
            'production' => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_PRODUCTION_ACCEPTED, self::STATUS_READY_TO_DISPATCHED],
            'dispatch' => [self::STATUS_READY_TO_DISPATCHED, self::STATUS_DISPATCHED, self::STATUS_DISPATCHED_DONE],
            'fitting' => [self::STATUS_DISPATCHED_DONE, self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
            'account' => [self::STATUS_IN_DESIGN, self::STATUS_QUOTATION_SENT, self::STATUS_QUOTATION_APPROVED, self::STATUS_ADVANCE_RECEIVED, self::STATUS_LEAD_REJECTED, self::STATUS_FITTING_DONE],
            default => self::allStatuses(),
        };
    }

    public static function transitionMap(): array
    {
        return [
            self::STATUS_IN_MEASUREMENT => [self::STATUS_MEASUREMENT_DONE],
            self::STATUS_MEASUREMENT_DONE => [self::STATUS_IN_DESIGN, self::STATUS_QUOTATION_SENT],
            self::STATUS_IN_DESIGN => [self::STATUS_QUOTATION_SENT, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_SENT => [self::STATUS_QUOTATION_APPROVED, self::STATUS_LEAD_REJECTED],
            self::STATUS_QUOTATION_APPROVED => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_LEAD_REJECTED],
            self::STATUS_ADVANCE_RECEIVED => [self::STATUS_PRODUCTION_ACCEPTED],
            self::STATUS_PRODUCTION_ACCEPTED => [self::STATUS_READY_TO_DISPATCHED],
            self::STATUS_READY_TO_DISPATCHED => [self::STATUS_DISPATCHED],
            self::STATUS_DISPATCHED => [self::STATUS_DISPATCHED_DONE],
            self::STATUS_DISPATCHED_DONE => [self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_PENDING => [self::STATUS_FITTING_DONE],
            self::STATUS_FITTING_DONE => [],
            self::STATUS_LEAD_REJECTED => [],
        ];
    }

    public static function allowedTransitionsFor(User $user, Lead $lead): array
    {
        $roleSlug = optional($user->crmRole)->slug;

        if ($roleSlug === 'storemanager') {
            return self::allStatuses();
        }

        $map = self::transitionMap();
        $transitions = $map[$lead->iCurrentLeadStatus] ?? [];
        $visible = self::roleQueueStatuses($roleSlug);

        return array_values(array_intersect($transitions, self::allStatuses(), array_merge($visible, $transitions)));
    }

    public static function canAccessLead(User $user, Lead $lead): bool
    {
        $roleSlug = optional($user->crmRole)->slug;

        if ($roleSlug === 'storemanager') {
            return true;
        }

        return in_array($lead->iCurrentLeadStatus, self::roleQueueStatuses($roleSlug), true);
    }

    public static function dashboardStatuses(?string $roleSlug): array
    {
        return match ($roleSlug) {
            'measurement' => [self::STATUS_IN_MEASUREMENT, self::STATUS_MEASUREMENT_DONE],
            'production' => [self::STATUS_ADVANCE_RECEIVED, self::STATUS_PRODUCTION_ACCEPTED, self::STATUS_READY_TO_DISPATCHED],
            'dispatch' => [self::STATUS_READY_TO_DISPATCHED, self::STATUS_DISPATCHED, self::STATUS_DISPATCHED_DONE],
            'fitting' => [self::STATUS_DISPATCHED_DONE, self::STATUS_FITTING_PENDING, self::STATUS_FITTING_DONE],
            'account' => [self::STATUS_QUOTATION_APPROVED, self::STATUS_ADVANCE_RECEIVED, self::STATUS_FITTING_DONE],
            default => [
                self::STATUS_IN_MEASUREMENT,
                self::STATUS_MEASUREMENT_DONE,
                self::STATUS_IN_DESIGN,
                self::STATUS_QUOTATION_SENT,
                self::STATUS_QUOTATION_APPROVED,
                self::STATUS_ADVANCE_RECEIVED,
            ],
        };
    }

    public static function fittingCollectedThisMonth(): array
    {
        return [self::STATUS_FITTING_DONE];
    }
}
