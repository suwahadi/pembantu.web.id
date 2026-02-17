<?php

namespace App\Domain\Payment\Support;

class MidtransMapper
{
    /**
     * Map status Midtrans ke internal payment status
     */
    public static function mapStatus(string $midtransStatus): string
    {
        return match($midtransStatus) {
            '100' => 'settlement',
            '201' => 'pending',
            '202' => 'pending',
            '407' => 'pending',
            '408' => 'pending',
            '203' => 'expire',
            '204' => 'deny',
            '401' => 'cancel',
            '402' => 'cancel',
            '406' => 'cancel',
            '419' => 'cancel',
            '412' => 'chargeback',
            '413' => 'chargeback',
            default => 'initiated',
        };
    }

    /**
     * Cek apakah status dianggap settled/successful
     */
    public static function isSettled(string $midtransStatus): bool
    {
        return in_array($midtransStatus, ['100']);
    }

    /**
     * Cek apakah status dianggap failed/reject
     */
    public static function isFailed(string $midtransStatus): bool
    {
        return in_array($midtransStatus, ['203', '204', '401', '402', '406', '419']);
    }
}
