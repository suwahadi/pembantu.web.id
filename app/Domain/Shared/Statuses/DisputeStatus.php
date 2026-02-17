<?php

namespace App\Domain\Shared\Statuses;

class DisputeStatus
{
    public const OPEN = 'open';
    public const INVESTIGATING = 'investigating';
    public const RESOLVED = 'resolved';
    public const REJECTED = 'rejected';

    public static function all(): array
    {
        return [
            self::OPEN,
            self::INVESTIGATING,
            self::RESOLVED,
            self::REJECTED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::OPEN => 'Terbuka',
            self::INVESTIGATING => 'Sedang Diinvestigasi',
            self::RESOLVED => 'Terselesaikan',
            self::REJECTED => 'Ditolak',
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
