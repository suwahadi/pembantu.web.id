<?php

namespace App\Domain\Shared\Statuses;

class PayoutStatus
{
    public const QUEUED = 'queued';
    public const PROCESSING = 'processing';
    public const PAID = 'paid';
    public const FAILED = 'failed';

    public static function all(): array
    {
        return [
            self::QUEUED,
            self::PROCESSING,
            self::PAID,
            self::FAILED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::QUEUED => 'Antrian',
            self::PROCESSING => 'Diproses',
            self::PAID => 'Dibayarkan',
            self::FAILED => 'Gagal',
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
