<?php

namespace App\Domain\Shared\Support;

class Idempotency
{
    /**
     * Generate unique entry key untuk ledger dan operasi penting
     * Format: prefix:order_id:action:ref_id
     */
    public static function generateEntryKey(string $prefix, string|int $orderId, string $action, string|int|null $refId = null): string
    {
        $key = "{$prefix}:{$orderId}:{$action}";
        if ($refId !== null) {
            $key .= ":{$refId}";
        }
        return $key;
    }

    /**
     * Payment settlement key
     */
    public static function paymentSettlementKey(string|int $orderId, string $transactionId): string
    {
        return self::generateEntryKey('ORDER', $orderId, 'PAYMENT_SETTLEMENT', $transactionId);
    }

    /**
     * Escrow hold key
     */
    public static function escrowHoldKey(string|int $orderId): string
    {
        return self::generateEntryKey('ORDER', $orderId, 'ESCROW_HOLD');
    }

    /**
     * Dispute refund decision key
     */
    public static function disputeRefundKey(string|int $orderId, string|int $disputeId): string
    {
        return self::generateEntryKey('ORDER', $orderId, 'REFUND_DECISION', $disputeId);
    }

    /**
     * Dispute release decision key
     */
    public static function disputeReleaseKey(string|int $orderId, string|int $disputeId): string
    {
        return self::generateEntryKey('ORDER', $orderId, 'RELEASE_DECISION', $disputeId);
    }

    /**
     * Refund paid key
     */
    public static function refundPaidKey(string|int $refundId): string
    {
        return "REFUND:{$refundId}:PAID";
    }

    /**
     * Payout paid key
     */
    public static function payoutPaidKey(string|int $payoutId): string
    {
        return "PAYOUT:{$payoutId}:PAID";
    }
}
