<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\Payment\Support\MidtransSignature;
use App\Domain\Payment\Support\MidtransMapper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MidtransNotificationController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Handle Midtrans notification/callback
     * POST dari Midtrans dengan payload transaksi
     * WAJIB verify signature sebelum proses
     */
    public function handleNotification(Request $request)
    {
        try {
            $payload = $request->all();

            // Verify signature dari Midtrans
            $serverKey = (string) config('midtrans.server_key');
            $signature = new MidtransSignature($serverKey);

            $orderId = (string) ($payload['order_id'] ?? '');
            $statusCode = (string) ($payload['status_code'] ?? '');
            $grossAmount = (string) ($payload['gross_amount'] ?? '0');
            $signatureKey = (string) ($payload['signature_key'] ?? '');

            if (!$signature->verify($orderId, $statusCode, $grossAmount, $signatureKey)) {
                \Log::warning('Midtrans signature verification failed', ['order_id' => $orderId]);
                return response()->json(['message' => 'Invalid signature'], Response::HTTP_UNAUTHORIZED);
            }

            $transactionId = (string) ($payload['transaction_id'] ?? '');
            $transactionStatus = (string) ($payload['transaction_status'] ?? '');

            if (MidtransMapper::isSettled($transactionStatus)) {
                // Settlement - successful
                $this->paymentService->handlePaymentSettlement(
                    $orderId,
                    $transactionId,
                    $payload,
                );
            } elseif (MidtransMapper::isFailed($transactionStatus)) {
                // Failed/expired/denied
                $this->paymentService->handlePaymentFailure(
                    $orderId,
                    $transactionStatus,
                    $payload,
                );
            }

            return response()->json(['message' => 'OK'], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Midtrans callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Processing failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
