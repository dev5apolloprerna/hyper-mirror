<?php

namespace App\Support;

use App\Enums\StockMovementType;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * Central place for every stock quantity change.
 *
 * All public methods here run inside their own DB transaction (unless one is
 * already open, in which case they simply participate in it) and lock the
 * relevant product_stocks row(s) to avoid race conditions when two requests
 * touch the same product/showroom at once.
 */
class StockManager
{
    /**
     * Current on-hand quantity for a product at a showroom (0 if never stocked).
     */
    public static function currentQuantity(int $productId, int $showroomId): int
    {
        return (int) ProductStock::where('iProductId', $productId)
            ->where('iShowroomId', $showroomId)
            ->value('iQuantity');
    }

    /**
     * Manually add stock (e.g. purchase / goods received).
     */
    public static function stockIn(int $productId, int $showroomId, int $qty, ?string $reason = null, ?int $userId = null): StockMovement
    {
        self::assertPositive($qty);

        return DB::transaction(function () use ($productId, $showroomId, $qty, $reason, $userId) {
            $balance = self::applyDelta($productId, $showroomId, $qty);

            return self::logMovement($productId, $showroomId, StockMovementType::IN, $qty, $balance, $reason, null, null, $userId);
        });
    }

    /**
     * Manually remove stock (e.g. damage, correction). Throws if not enough stock.
     */
    public static function stockOut(int $productId, int $showroomId, int $qty, ?string $reason = null, ?int $userId = null): StockMovement
    {
        self::assertPositive($qty);

        return DB::transaction(function () use ($productId, $showroomId, $qty, $reason, $userId) {
            self::assertSufficientStock($productId, $showroomId, $qty);
            $balance = self::applyDelta($productId, $showroomId, -$qty);

            return self::logMovement($productId, $showroomId, StockMovementType::OUT, $qty, $balance, $reason, null, null, $userId);
        });
    }

    /**
     * Move stock from one showroom to another. Throws if the source showroom
     * does not have enough stock.
     *
     * @return array{out: StockMovement, in: StockMovement}
     */
    public static function transfer(int $productId, int $fromShowroomId, int $toShowroomId, int $qty, ?string $reason = null, ?int $userId = null): array
    {
        self::assertPositive($qty);

        if ($fromShowroomId === $toShowroomId) {
            throw new \InvalidArgumentException('Source and destination showroom must be different.');
        }

        return DB::transaction(function () use ($productId, $fromShowroomId, $toShowroomId, $qty, $reason, $userId) {
            self::assertSufficientStock($productId, $fromShowroomId, $qty);

            $fromBalance = self::applyDelta($productId, $fromShowroomId, -$qty);
            $toBalance   = self::applyDelta($productId, $toShowroomId, $qty);

            $out = self::logMovement($productId, $fromShowroomId, StockMovementType::TRANSFER_OUT, $qty, $fromBalance, $reason, null, null, $userId, $toShowroomId);
            $in  = self::logMovement($productId, $toShowroomId, StockMovementType::TRANSFER_IN, $qty, $toBalance, $reason, null, null, $userId, $fromShowroomId);

            return ['out' => $out, 'in' => $in];
        });
    }

    /**
     * Deduct stock because it was sold on an invoice. Sales are not blocked
     * by low/zero stock (the quantity is simply allowed to go negative) so
     * that invoicing is never held up by a stock-count mismatch.
     */
    public static function deductForInvoice(int $productId, int $showroomId, int $qty, int $invoiceId, ?int $userId = null): StockMovement
    {
        self::assertPositive($qty);

        return DB::transaction(function () use ($productId, $showroomId, $qty, $invoiceId, $userId) {
            $balance = self::applyDelta($productId, $showroomId, -$qty);

            return self::logMovement($productId, $showroomId, StockMovementType::INVOICE_OUT, $qty, $balance, null, $invoiceId, 'invoice', $userId);
        });
    }

    /**
     * Reverse every stock deduction that was made for a given invoice
     * (used when an invoice is cancelled/deleted).
     */
    public static function reverseInvoiceDeductions(int $invoiceId, ?int $userId = null): void
    {
        $movements = StockMovement::where('strReferenceType', 'invoice')
            ->where('iReferenceId', $invoiceId)
            ->where('strType', StockMovementType::INVOICE_OUT)
            ->get();

        foreach ($movements as $movement) {
            DB::transaction(function () use ($movement, $userId) {
                $balance = self::applyDelta($movement->iProductId, $movement->iShowroomId, $movement->iQuantity);

                self::logMovement(
                    $movement->iProductId,
                    $movement->iShowroomId,
                    StockMovementType::INVOICE_REVERSAL,
                    $movement->iQuantity,
                    $balance,
                    'Invoice #' . $movement->iReferenceId . ' cancelled/deleted',
                    $movement->iReferenceId,
                    'invoice',
                    $userId
                );
            });
        }
    }

    // -------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------

    private static function assertPositive(int $qty): void
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero.');
        }
    }

    private static function assertSufficientStock(int $productId, int $showroomId, int $qty): void
    {
        $available = self::currentQuantity($productId, $showroomId);

        if ($available < $qty) {
            throw new \RuntimeException("Not enough stock available. Available: {$available}, requested: {$qty}.");
        }
    }

    /**
     * Locks (or creates) the product_stocks row and applies the delta,
     * returning the resulting balance.
     */
    private static function applyDelta(int $productId, int $showroomId, int $delta): int
    {
        $stock = ProductStock::where('iProductId', $productId)
            ->where('iShowroomId', $showroomId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            $stock = ProductStock::create([
                'iProductId'  => $productId,
                'iShowroomId' => $showroomId,
                'iQuantity'   => 0,
            ]);
        }

        $stock->iQuantity += $delta;
        $stock->save();

        return $stock->iQuantity;
    }

    private static function logMovement(
        int $productId,
        int $showroomId,
        string $type,
        int $qty,
        int $balanceAfter,
        ?string $reason,
        ?int $referenceId,
        ?string $referenceType,
        ?int $userId,
        ?int $relatedShowroomId = null
    ): StockMovement {
        return StockMovement::create([
            'iProductId'         => $productId,
            'iShowroomId'        => $showroomId,
            'iRelatedShowroomId' => $relatedShowroomId,
            'strType'            => $type,
            'iQuantity'          => $qty,
            'iBalanceAfter'      => $balanceAfter,
            'strReason'          => $reason,
            'iReferenceId'       => $referenceId,
            'strReferenceType'   => $referenceType,
            'iCreatedBy'         => $userId,
        ]);
    }
}
