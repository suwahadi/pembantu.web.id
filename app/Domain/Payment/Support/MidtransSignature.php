<?php

namespace App\Domain\Payment\Support;

class MidtransSignature
{
    private string $serverKey;

    public function __construct(string $serverKey)
    {
        $this->serverKey = $serverKey;
    }

    /**
     * Verifikasi signature dari notification Midtrans
     */
    public function verify(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $data = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $hash = hash('sha512', $data);
        return hash_equals($hash, $signatureKey);
    }

    /**
     * Generate signature untuk request ke Midtrans (jika diperlukan)
     */
    public function generate(string $orderId, string $grossAmount): string
    {
        $data = $orderId . $grossAmount . $this->serverKey;
        return hash('sha512', $data);
    }
}
