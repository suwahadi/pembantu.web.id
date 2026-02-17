<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\Payment\Support\MidtransMapper;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Handle Midtrans notification/callback
     * POST dari Midtrans dengan payload transaksi
     */
    public function handle(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $transactionId = $request->input('transaction_id');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');

            $internalStatus = MidtransMapper::mapStatus($statusCode);

            if (MidtransMapper::isSettled($statusCode)) {
                // Settlement - successful
                $this->paymentService->handlePaymentSettlement(
                    $orderId,
                    $transactionId,
                    $request->all(),
                );
            } elseif (MidtransMapper::isFailed($statusCode)) {
                // Failed/expired/denied
                $this->paymentService->handlePaymentFailure(
                    $orderId,
                    $internalStatus,
                    $request->all(),
                );
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            \Log::error('Midtrans callback error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
