<?php

namespace App\Domain\Shared\Statuses;

class EscrowStatus
{
    public const HOLD = 'hold';
    public const RELEASED = 'released';
    public const REFUNDED = 'refunded';
    public const PARTIAL_REFUNDED = 'partial_refunded';
    public const PARTIAL_RELEASED = 'partial_released';

    public static function all(): array
    {
        return [
            self::HOLD,
            self::RELEASED,
            self::REFUNDED,
            self::PARTIAL_REFUNDED,
            self::PARTIAL_RELEASED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::HOLD => 'Ditahan (Escrow)',
            self::RELEASED => 'Dilepaskan',
            self::REFUNDED => 'Dikembalikan Penuh',
            self::PARTIAL_REFUNDED => 'Dikembalikan Sebagian',
            self::PARTIAL_RELEASED => 'Dilepaskan Sebagian',
        ];
    }

    public static function label(string $status): string
    {
        return self::labels()[$status] ?? $status;
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::all());
    }
}
