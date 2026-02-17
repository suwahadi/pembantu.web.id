<?php

namespace App\Domain\Shared\Statuses;

class RefundStatus
{
    public const QUEUED = 'queued';
    public const PROCESSING = 'processing';
    public const PAID = 'paid';
    public const FAILED = 'failed';
    public const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::QUEUED,
            self::PROCESSING,
            self::PAID,
            self::FAILED,
            self::CANCELLED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::QUEUED => 'Antrian',
            self::PROCESSING => 'Diproses',
            self::PAID => 'Dibayarkan',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
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
