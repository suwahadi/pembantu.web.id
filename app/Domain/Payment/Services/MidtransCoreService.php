<?php

namespace App\Domain\Payment\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
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
     * Charge an order via Snap
     * Returns the Snap response containing redirect_url
     */
    public function charge(int $orderId): array
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
        ];

        try {
            // Get Snap URL directly
            $redirectUrl = Snap::createTransaction($params)->redirect_url;

            return [
                'redirect_url' => $redirectUrl,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans Charge Exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Gagal memproses pembayaran ke Midtrans: ' . $e->getMessage());
        }
    }
}
