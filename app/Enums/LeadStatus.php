<?php

namespace App\Enums;

class LeadStatus
{
    const IN_MEASUREMENT      = 1;
    const MEASUREMENT_DONE    = 2;
    const IN_DESIGN           = 3;
    const QUOTATION_SENT      = 4;
    const QUOTATION_APPROVED  = 5;
    const ADVANCE_RECEIVED    = 6;
    const PRODUCTION_PENDING  = 7;
    const READY_TO_DISPATCH   = 8;
    const DISPATCHED          = 9;
    const FITTING_DONE        = 10;
    const LEAD_REJECTED       = 11;

    // -------------------------------------------------------
    // Labels
    // -------------------------------------------------------

    public static function label(int $status): string
    {
        return self::labels()[$status] ?? 'Unknown';
    }

    public static function labels(): array
    {
        return [
            self::IN_MEASUREMENT     => 'In Measurement',
            self::MEASUREMENT_DONE   => 'Measurement Done',
            self::IN_DESIGN          => 'In Design',
            self::QUOTATION_SENT     => 'Quotation Sent',
            self::QUOTATION_APPROVED => 'Quotation Approved',
            self::ADVANCE_RECEIVED   => 'Advance Received',
            self::PRODUCTION_PENDING => 'Production Pending',
            self::READY_TO_DISPATCH  => 'Ready to Dispatch',
            self::DISPATCHED         => 'Dispatched',
            self::FITTING_DONE       => 'Fitting Done',
            self::LEAD_REJECTED      => 'Lead Rejected',
        ];
    }

    // -------------------------------------------------------
    // Badge colours
    // -------------------------------------------------------

    public static function badgeClass(int $status): string
    {
        return [
            self::IN_MEASUREMENT     => 'bg-warning text-dark',
            self::MEASUREMENT_DONE   => 'bg-info text-dark',
            self::IN_DESIGN          => 'bg-primary',
            self::QUOTATION_SENT     => 'bg-secondary',
            self::QUOTATION_APPROVED => 'bg-success',
            self::ADVANCE_RECEIVED   => 'bg-success',
            self::PRODUCTION_PENDING => 'bg-warning text-dark',
            self::READY_TO_DISPATCH  => 'bg-info text-dark',
            self::DISPATCHED         => 'bg-primary',
            self::FITTING_DONE       => 'bg-success',
            self::LEAD_REJECTED      => 'bg-danger',
        ][$status] ?? 'bg-secondary';
    }

    // -------------------------------------------------------
    // Date configuration per status
    // -------------------------------------------------------

    /**
     * Returns date field config when transitioning INTO a status.
     * [
     *   'required' => bool,
     *   'label'    => string shown in UI,
     *   'field'    => which leads column to save into,
     * ]
     * Returns null if no date needed.
     */
    public static function dateConfig(int $toStatus): ?array
    {
        return match ($toStatus) {
            self::IN_MEASUREMENT     => [
                'required' => true,
                'label'    => 'Measurement Visit Date',
                'field'    => 'MeasurementVisitDate',
            ],
            self::IN_DESIGN          => [
                'required' => true,
                'label'    => 'Next Followup Date',
                'field'    => 'NetFollowupdate',
            ],
            self::QUOTATION_SENT     => [
                'required' => true,
                'label'    => 'Next Followup Date',
                'field'    => 'NetFollowupdate',
            ],
            self::QUOTATION_APPROVED => [
                'required' => true,
                'label'    => 'Advance Payment Followup Date',
                'field'    => 'NetFollowupdate',
            ],
            self::ADVANCE_RECEIVED   => [
                'required' => true,
                'label'    => 'Expected Production Date',
                'field'    => 'NetFollowupdate',
            ],
            self::READY_TO_DISPATCH  => [
                'required' => true,
                'label'    => 'Dispatched Date',
                'field'    => 'DispatchedDate',
            ],
            self::DISPATCHED         => [
                'required' => true,
                'label'    => 'Fitting Date',
                'field'    => 'FittingDate',
            ],
            default => null,
        };
    }

    public static function requiresDate(int $toStatus): bool
    {
        return self::dateConfig($toStatus) !== null;
    }

    /**
     * All date configs as JSON for Blade/JS dynamic UI.
     * Shape: { "8": { required: true, label: "Dispatched Date", field: "DispatchedDate" }, ... }
     */
    public static function dateConfigsJson(): string
    {
        $map = [];
        foreach (array_keys(self::labels()) as $status) {
            $config = self::dateConfig($status);
            if ($config) {
                $map[$status] = $config;
            }
        }
        return json_encode($map);
    }

    // -------------------------------------------------------
    // Transition rules per role
    // -------------------------------------------------------

    public static function allowedTransitions(string $roleSlug): array
    {
        return match ($roleSlug) {

            'storemanager' => [
                self::MEASUREMENT_DONE   => [self::IN_DESIGN, self::QUOTATION_SENT, self::LEAD_REJECTED],
                self::IN_DESIGN          => [self::QUOTATION_SENT, self::LEAD_REJECTED],
                self::QUOTATION_SENT     => [self::QUOTATION_APPROVED, self::LEAD_REJECTED],
                self::QUOTATION_APPROVED => [self::ADVANCE_RECEIVED, self::LEAD_REJECTED],
            ],

            'measurement' => [
                self::IN_MEASUREMENT => [self::MEASUREMENT_DONE],
            ],

            'production' => [
                self::ADVANCE_RECEIVED   => [self::PRODUCTION_PENDING],
                self::PRODUCTION_PENDING => [self::READY_TO_DISPATCH],
            ],

            'dispatch' => [
                self::READY_TO_DISPATCH => [self::DISPATCHED],
            ],

            'fitting' => [
                self::DISPATCHED => [self::FITTING_DONE],
            ],

            'account' => [], // Account adds payments only

            default => [],
        };
    }

    public static function nextStatuses(string $roleSlug, int $currentStatus): array
    {
        return self::allowedTransitions($roleSlug)[$currentStatus] ?? [];
    }
}
