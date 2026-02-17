<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Domain\Payment\Services\MidtransWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handle Midtrans webhook callbacks
 * IMPORTANT: CSRF disabled untuk endpoint ini karena Midtrans tidak send CSRF token
 */
final class MidtransWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        MidtransWebhookService $webhookService
    ) {
        try {
            $payload = $request->all();

            Log::debug('Midtrans webhook received', ['payload' => $payload]);

            // Process webhook
            $webhookService->handleCallback($payload);

            // Return 200 OK untuk acknowledge Midtrans
            return response()->json(['status' => 'ok'], 200);
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook processing error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            // Return 200 anyway untuk prevent Midtrans retry (sudah dicatat di log)
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}
