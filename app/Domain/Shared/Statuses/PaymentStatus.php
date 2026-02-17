<?php

namespace App\Domain\Shared\Statuses;

class PaymentStatus
{
    public const INITIATED = 'initiated';
    public const PENDING = 'pending';
    public const SETTLEMENT = 'settlement';
    public const EXPIRE = 'expire';
    public const CANCEL = 'cancel';
    public const DENY = 'deny';
    public const CHARGEBACK = 'chargeback';

    public static function all(): array
    {
        return [
            self::INITIATED,
            self::PENDING,
            self::SETTLEMENT,
            self::EXPIRE,
            self::CANCEL,
            self::DENY,
            self::CHARGEBACK,
        ];
    }

    public static function labels(): array
    {
        return [
            self::INITIATED => 'Dimulai',
            self::PENDING => 'Tertunda',
            self::SETTLEMENT => 'Settled',
            self::EXPIRE => 'Waktu Habis',
            self::CANCEL => 'Dibatalkan',
            self::DENY => 'Ditolak',
            self::CHARGEBACK => 'Chargeback',
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

    public static function isSuccessful(string $status): bool
    {
        return $status === self::SETTLEMENT;
    }

    public static function isFailure(string $status): bool
    {
        return in_array($status, [self::EXPIRE, self::CANCEL, self::DENY, self::CHARGEBACK]);
    }
}
