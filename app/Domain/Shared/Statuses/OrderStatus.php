<?php

namespace App\Domain\Shared\Statuses;

class OrderStatus
{
    public const PENDING_PAYMENT = 'pending_payment';
    public const PAID_ESCROW = 'paid_escrow';
    public const IN_PROGRESS = 'in_progress';
    public const COMPLETED_BY_AGENCY = 'completed_by_agency';
    public const COMPLETED = 'completed';
    public const DISPUTED = 'disputed';
    public const REFUND_PENDING = 'refund_pending';
    public const PAYOUT_PENDING = 'payout_pending';
    public const REFUNDED = 'refunded';
    public const PARTIALLY_REFUNDED = 'partially_refunded';
    public const RELEASED = 'released';
    public const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::PENDING_PAYMENT,
            self::PAID_ESCROW,
            self::IN_PROGRESS,
            self::COMPLETED_BY_AGENCY,
            self::COMPLETED,
            self::DISPUTED,
            self::REFUND_PENDING,
            self::PAYOUT_PENDING,
            self::REFUNDED,
            self::PARTIALLY_REFUNDED,
            self::RELEASED,
            self::CANCELLED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::PAID_ESCROW => 'Pembayaran Terverifikasi',
            self::IN_PROGRESS => 'Sedang Berjalan',
            self::COMPLETED_BY_AGENCY => 'Selesai oleh Agency',
            self::COMPLETED => 'Selesai',
            self::DISPUTED => 'Dalam Sengketa',
            self::REFUND_PENDING => 'Pengembalian Dana Tertunda',
            self::PAYOUT_PENDING => 'Pencairan Dana Tertunda',
            self::REFUNDED => 'Dana Dikembalikan',
            self::PARTIALLY_REFUNDED => 'Dana Dikembalikan Sebagian',
            self::RELEASED => 'Dana Dilepas',
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
