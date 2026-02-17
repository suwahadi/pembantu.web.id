<?php

namespace App\Http\Middleware;

use App\Domain\Payment\Support\MidtransSignature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMidtransSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        // Untuk Midtrans callback verification
        if ($request->isMethod('post')) {
            $serverKey = config('midtrans.server_key');
            $verifier = new MidtransSignature($serverKey);

            $orderId = $request->input('order_id');
            $statusCode = $request->input('status_code');
            $grossAmount = $request->input('gross_amount');
            $signatureKey = $request->input('signature_key');

            if (!$verifier->verify($orderId, $statusCode, (string) $grossAmount, $signatureKey)) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        return $next($request);
    }
}
