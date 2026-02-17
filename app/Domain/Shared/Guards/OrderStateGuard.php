<?php

namespace App\Domain\Shared\Guards;

use App\Domain\Shared\Statuses\OrderStatus;

/**
 * State Machine Guard untuk validasi transisi status order
 * Mencegah transisi status yang tidak valid
 */
final class OrderStateGuard
{
    /**
     * Define valid transitions: dari_status => [ke_status_list]
     */
    private const TRANSITIONS = [
        OrderStatus::PENDING_PAYMENT => [OrderStatus::PAID_ESCROW, OrderStatus::CANCELLED],
        OrderStatus::PAID_ESCROW => [OrderStatus::IN_PROGRESS, OrderStatus::DISPUTED, OrderStatus::CANCELLED],
        OrderStatus::IN_PROGRESS => [OrderStatus::COMPLETED_BY_AGENCY, OrderStatus::DISPUTED],
        OrderStatus::COMPLETED_BY_AGENCY => [OrderStatus::COMPLETED, OrderStatus::DISPUTED],
        OrderStatus::COMPLETED => [OrderStatus::DISPUTED],
        OrderStatus::DISPUTED => [OrderStatus::COMPLETED, OrderStatus::REFUND_PENDING, OrderStatus::PAYOUT_PENDING, OrderStatus::CANCELLED],
        OrderStatus::REFUND_PENDING => [OrderStatus::REFUNDED, OrderStatus::PARTIALLY_REFUNDED, OrderStatus::CANCELLED],
        OrderStatus::PAYOUT_PENDING => [OrderStatus::RELEASED, OrderStatus::CANCELLED],
        OrderStatus::REFUNDED => [], // Terminal state
        OrderStatus::PARTIALLY_REFUNDED => [OrderStatus::RELEASED], // Can still release remaining
        OrderStatus::RELEASED => [], // Terminal state
        OrderStatus::CANCELLED => [], // Terminal state
    ];

    /**
     * Check if transition is valid
     */
    public static function canTransition(string $fromStatus, string $toStatus): bool
    {
        if (!OrderStatus::isValid($fromStatus) || !OrderStatus::isValid($toStatus)) {
            return false;
        }

        if ($fromStatus === $toStatus) {
            return false; // No self-transition
        }

        return in_array($toStatus, self::TRANSITIONS[$fromStatus] ?? []);
    }

    /**
     * Get allowed transitions from current status
     */
    public static function getAllowedTransitions(string $status): array
    {
        return self::TRANSITIONS[$status] ?? [];
    }

    /**
     * Validate and throw if transition not allowed
     */
    public static function validate(string $fromStatus, string $toStatus): void
    {
        if (!self::canTransition($fromStatus, $toStatus)) {
            throw new \RuntimeException(
                "Transisi status order tidak valid: {$fromStatus} → {$toStatus}"
            );
        }
    }
}
