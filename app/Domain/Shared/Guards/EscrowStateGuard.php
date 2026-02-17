<?php

namespace App\Domain\Shared\Guards;

use App\Domain\Shared\Statuses\EscrowStatus;

/**
 * State Machine Guard untuk Escrow Holds
 * Escrow hanya boleh final state, tidak boleh kembali ke hold
 */
final class EscrowStateGuard
{
    private const TRANSITIONS = [
        EscrowStatus::HOLD => [
            EscrowStatus::RELEASED,
            EscrowStatus::REFUNDED,
            EscrowStatus::PARTIAL_REFUNDED,
            EscrowStatus::PARTIAL_RELEASED,
        ],
        EscrowStatus::RELEASED => [], // Terminal
        EscrowStatus::REFUNDED => [], // Terminal
        EscrowStatus::PARTIAL_REFUNDED => [EscrowStatus::PARTIAL_RELEASED], // Sisa bisa di-release
        EscrowStatus::PARTIAL_RELEASED => [], // Terminal
    ];

    public static function canTransition(string $fromStatus, string $toStatus): bool
    {
        if ($fromStatus === $toStatus) return false;
        return in_array($toStatus, self::TRANSITIONS[$fromStatus] ?? []);
    }

    public static function validate(string $fromStatus, string $toStatus): void
    {
        if (!self::canTransition($fromStatus, $toStatus)) {
            throw new \RuntimeException(
                "Transisi escrow tidak valid: {$fromStatus} → {$toStatus}. Escrow bersifat final."
            );
        }
    }

    /**
     * Check if escrow is in final state (tidak bisa berubah lagi)
     */
    public static function isFinal(string $status): bool
    {
        return empty(self::TRANSITIONS[$status] ?? []);
    }
}
