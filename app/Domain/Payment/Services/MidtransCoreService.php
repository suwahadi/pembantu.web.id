<?php

namespace App\Domain\Payment\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\Log;

class MidtransCoreService
{
    public function __construct()
    {
        Config::$serverKey = (string)config('midtrans.server_key');
        Config::$isProduction = (bool)config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Charge an order via Midtrans Core API
     * Returns the charge response containing payment details
     */
    public function charge(int $orderId, string $paymentType, ?string $bank = null): array
    {
        $order = Order::with(['visitor', 'worker'])->findOrFail($orderId);

        $params = [
            'transaction_details' => [
                'order_id' => 'order_' . $order->id . '_' . time(),
                'gross_amount' => (int)$order->total_idr,
            ],
            'customer_details' => [
                'first_name' => $order->visitor->name,
                'email' => $order->visitor->email,
            ],
            'item_details' => [
                [
                    'id' => 'worker_' . $order->worker_id,
                    'price' => (int)$order->total_idr,
                    'quantity' => 1,
                    'name' => 'Layanan Tenaga Kerja - ' . ($order->worker->name ?? 'Service'),
                ]
            ],
            'payment_type' => $paymentType,
        ];

        if ($paymentType === 'bank_transfer' && $bank) {
            $params['bank_transfer'] = ['bank' => $bank];
        } elseif ($paymentType === 'gopay') {
            $params['payment_type'] = 'gopay';
        }

        try {
            $response = CoreApi::charge($params);

            return json_decode(json_encode($response), true);
        } catch (\Throwable $e) {
            Log::error('Midtrans Charge Exception', [
                'order_id' => $orderId,
                'payment_type' => $paymentType,
                'message' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Gagal memproses pembayaran ke Midtrans: ' . $e->getMessage());
        }
    }
}
