<?php

namespace App\Domain\Payment\Support;

class MidtransMapper
{
    /**
     * Map status Midtrans ke internal payment status
     */
    public static function mapStatus(string $midtransStatus): string
    {
        $status = strtolower($midtransStatus);

        return match($status) {
            '100', 'settlement', 'capture' => 'settlement',
            '201', '202', '407', '408', 'pending' => 'pending',
            '203', 'expire' => 'expire',
            '204', 'deny' => 'deny',
            '401', '402', '406', '419', 'cancel' => 'cancel',
            '412', '413', 'chargeback', 'refund' => 'chargeback',
            default => 'initiated',
        };
    }

    /**
     * Cek apakah status dianggap settled/successful
     */
    public static function isSettled(string $midtransStatus): bool
    {
        $status = strtolower($midtransStatus);

        return in_array($status, ['100', 'settlement', 'capture'], true);
    }

    /**
     * Cek apakah status dianggap failed/reject
     */
    public static function isFailed(string $midtransStatus): bool
    {
        $status = strtolower($midtransStatus);

        return in_array($status, ['203', '204', '401', '402', '406', '419', 'expire', 'cancel', 'deny', 'failure'], true);
    }
}
