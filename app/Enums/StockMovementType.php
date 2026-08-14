<?php

namespace App\Enums;

class StockMovementType
{
    const IN               = 'in';               // manual stock received (e.g. purchase)
    const OUT              = 'out';               // manual stock removed (e.g. damage, correction)
    const TRANSFER_IN      = 'transfer_in';        // received from another showroom
    const TRANSFER_OUT     = 'transfer_out';       // sent to another showroom
    const INVOICE_OUT      = 'invoice_out';        // deducted automatically because of an invoice
    const INVOICE_REVERSAL = 'invoice_reversal';   // added back because an invoice was cancelled/deleted

    // -------------------------------------------------------
    // Labels
    // -------------------------------------------------------

    public static function label(string $type): string
    {
        return self::labels()[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public static function labels(): array
    {
        return [
            self::IN               => 'Stock In',
            self::OUT               => 'Stock Out',
            self::TRANSFER_IN       => 'Transfer In',
            self::TRANSFER_OUT      => 'Transfer Out',
            self::INVOICE_OUT       => 'Invoice Sale',
            self::INVOICE_REVERSAL  => 'Invoice Reversal',
        ];
    }

    // -------------------------------------------------------
    // Badge colours
    // -------------------------------------------------------

    public static function badgeClass(string $type): string
    {
        return [
            self::IN                => 'bg-success',
            self::OUT                => 'bg-danger',
            self::TRANSFER_IN        => 'bg-info text-dark',
            self::TRANSFER_OUT       => 'bg-warning text-dark',
            self::INVOICE_OUT        => 'bg-primary',
            self::INVOICE_REVERSAL   => 'bg-secondary',
        ][$type] ?? 'bg-secondary';
    }

    // -------------------------------------------------------
    // Types that increase the showroom's quantity vs decrease it
    // -------------------------------------------------------

    public static function isInbound(string $type): bool
    {
        return in_array($type, [self::IN, self::TRANSFER_IN, self::INVOICE_REVERSAL], true);
    }

    public static function isOutbound(string $type): bool
    {
        return in_array($type, [self::OUT, self::TRANSFER_OUT, self::INVOICE_OUT], true);
    }
}
