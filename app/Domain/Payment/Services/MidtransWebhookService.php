<?php

namespace App\Domain\Payment\Services;

use App\Models\{Order, PaymentAttempt};
use App\Domain\Shared\Statuses\{OrderStatus, PaymentStatus};
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Midtrans Webhook Handler
 * Proses pembayaran dari callback Midtrans
 * Ini adalah komponen KRUSIAL - harus idempotent dan aman
 */
final class MidtransWebhookService
{
    public function __construct(private OrderEventService $eventService) {}

    /**
     * Handle webhook dari Midtrans
     * IDEMPOTENT: bisa dipanggil berkali-kali dengan result sama
     */
    public function handleCallback(array $payload): void
    {
        // 1. Validasi signature
        $this->validateSignature($payload);

        // 2. Extract data penting
        $orderId = $this->extractOrderId($payload['order_id'] ?? null);
        $transactionStatusRaw = $payload['transaction_status'] ?? null;
        $transactionStatus = $transactionStatusRaw !== null
            ? strtolower((string)$transactionStatusRaw)
            : null;
        $transactionId = $payload['transaction_id'] ?? null;

        if (!$orderId) {
            Log::warning('Midtrans webhook: order_id tidak valid', ['payload' => $payload]);
            return; // Ignore, jangan throw
        }

        // 3. Cek atau buat payment_attempts record (idempotency)
        $attempt = $this->recordPaymentAttempt($orderId, $payload, $transactionStatus);

        // 4. Jika sudah processed, skip (idempotent)
        if ($attempt->processed_at && $transactionStatus !== null && $attempt->status === $transactionStatus) {
            Log::info('Midtrans webhook: status sudah diproses sebelumnya', [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'status' => $transactionStatus,
            ]);
            return;
        }

        // 5. Process berdasarkan transaction_status
        try {
            $this->processPaymentStatus($orderId, $transactionStatus, $payload);

            // 6. Mark as processed
            $attempt->update([
                'transaction_id' => $transactionId,
                'status' => $transactionStatus ?? $attempt->status,
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            $attempt->update([
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Validate Midtrans signature
     */
    private function validateSignature(array $payload): void
    {
        $expectedSignature = $payload['signature_key'] ?? null;
        if (!$expectedSignature) {
            throw new \RuntimeException('Signature key tidak ditemukan');
        }

        $serverKey = config('midtrans.server_key');
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';

        $calculatedSignature = hash(
            'sha512',
            "{$orderId}{$statusCode}{$grossAmount}{$serverKey}"
        );

        if (hash_equals($calculatedSignature, $expectedSignature) === false) {
            throw new \RuntimeException('Signature tidak valid');
        }
    }

    /**
     * Extract order ID dari Midtrans order_id (format: order_123_visitor_456)
     */
    private function extractOrderId(?string $midtransOrderId): ?int
    {
        if (!$midtransOrderId) return null;

        // Format: order_{orderId}_visitor_{visitorId}
        if (preg_match('/^order_(\d+)_/', $midtransOrderId, $matches)) {
            return (int)$matches[1];
        }

        return null;
    }

    /**
     * Record payment attempt (untuk idempotency)
     */
    private function recordPaymentAttempt(int $orderId, array $payload, ?string $transactionStatus = null): PaymentAttempt
    {
        $midtransOrderId = $payload['order_id'] ?? null;
        $grossAmount = (int)($payload['gross_amount'] ?? 0);
        $transactionId = $payload['transaction_id'] ?? null;

        // Cek apakah sudah ada record
        $existing = PaymentAttempt::where('order_id', $orderId)
            ->where('midtrans_order_id', $midtransOrderId)
            ->first();

        if ($existing) {
            $statusChanged = $transactionStatus !== null && $transactionStatus !== $existing->status;

            // Update payload/status jika status berubah (biarkan idempotent check di handleCallback)
            $existing->update([
                'status' => $transactionStatus ?? $existing->status,
                'transaction_id' => $transactionId ?? $existing->transaction_id,
                'raw_payload' => $payload,
                'processed_at' => $statusChanged ? null : $existing->processed_at,
                'error_message' => null,
            ]);

            return $existing;
        }

        // Buat record baru
        return PaymentAttempt::create([
            'order_id' => $orderId,
            'midtrans_order_id' => $midtransOrderId,
            'amount_idr' => $grossAmount,
            'status' => $transactionStatus ?? $payload['transaction_status'] ?? 'unknown',
            'raw_payload' => $payload,
            'callback_received_at' => now(),
        ]);
    }

    /**
     * Process pembayaran berdasarkan status dari Midtrans
     */
    private function processPaymentStatus(int $orderId, ?string $transactionStatus, array $payload): void
    {
        DB::transaction(function () use ($orderId, $transactionStatus, $payload) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Map Midtrans status ke internal status
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    $this->handlePaymentSuccess($order, $payload);
                    break;

                case 'pending':
                    $this->handlePaymentPending($order, $payload);
                    break;

                case 'expire':
                case 'cancel':
                case 'deny':
                    $this->handlePaymentFailure($order, $transactionStatus, $payload);
                    break;

                case 'refund':
                case 'chargeback':
                    $this->handlePaymentRefund($order, $transactionStatus, $payload);
                    break;

                default:
                    Log::warning('Midtrans transaction_status tidak dikenal', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus,
                    ]);
            }
        });
    }

    /**
     * Payment berhasil (settlement/capture) → buat escrow hold
     */
    private function handlePaymentSuccess(Order $order, array $payload): void
    {
        // Validasi order masih pending_payment
        if ($order->status !== OrderStatus::PENDING_PAYMENT) {
            Log::info('Midtrans success: order sudah berstatus berbeda', [
                'order_id' => $order->id,
                'current_status' => $order->status,
            ]);
            return; // Idempotent: skip jika sudah berubah
        }

        $grossAmount = (int)($payload['gross_amount'] ?? 0);

        // Update order ke PAID_ESCROW
        $order->update(['status' => OrderStatus::PAID_ESCROW]);

        // Create escrow hold (jika belum ada)
        if (!$order->escrow) {
            DB::table('escrow_holds')->insert([
                'order_id' => $order->id,
                'amount_idr' => $grossAmount,
                'status' => 'hold',
                'held_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Record event
        $this->eventService->recordStatusChange(
            $order->id,
            OrderStatus::PENDING_PAYMENT,
            OrderStatus::PAID_ESCROW,
            description: 'Pembayaran berhasil dari Midtrans'
        );

        AuditLogService::record('payment_success', 'ORDER', $order->id, ['amount' => $grossAmount]);

        // Send notification setelah commit
        DB::afterCommit(function () use ($order) {
            $visitor = $order->visitor;
            if ($visitor) {
                $visitor->notify(new OrderStatusChanged(
                    orderId: $order->id,
                    orderCode: $order->code,
                    newStatus: OrderStatus::PAID_ESCROW,
                    message: 'Pembayaran Anda telah dikonfirmasi. Pekerjaan siap dimulai.'
                ));
            }
        });

        Log::info('Midtrans payment success', [
            'order_id' => $order->id,
            'amount' => $grossAmount,
        ]);
    }

    /**
     * Payment pending → tungu completion (biasanya untuk transfer bank manual)
     */
    private function handlePaymentPending(Order $order, array $payload): void
    {
        // Tetap di PENDING_PAYMENT, tunggu user untuk complete manual transfer
        // atau tunggu callback berikutnya dengan status settlement
        Log::info('Midtrans payment pending', [
            'order_id' => $order->id,
            'method' => $payload['payment_type'] ?? 'unknown',
        ]);
    }

    /**
     * Payment gagal → cancel order & release escrow jika ada
     */
    private function handlePaymentFailure(Order $order, string $failureType, array $payload): void
    {
        // Jika order bukan pending_payment lagi, skip
        if (!in_array($order->status, [OrderStatus::PENDING_PAYMENT, OrderStatus::PAID_ESCROW])) {
            return;
        }

        $order->update(['status' => OrderStatus::CANCELLED]);

        // Jika ada escrow, release
        if ($order->escrow) {
            DB::table('escrow_holds')
                ->where('order_id', $order->id)
                ->update([
                    'status' => 'refunded',
                    'refunded_at' => now(),
                ]);
        }

        // Record event
        $this->eventService->record(
            $order->id,
            'payment_failed',
            "Pembayaran gagal ({$failureType})"
        );

        AuditLogService::record('payment_failed', 'ORDER', $order->id, ['reason' => $failureType]);

        Log::warning('Midtrans payment failed', [
            'order_id' => $order->id,
            'failure_type' => $failureType,
        ]);
    }

    /**
     * Refund/Chargeback → log only (refund manual dihandle di admin panel)
     */
    private function handlePaymentRefund(Order $order, string $refundType, array $payload): void
    {
        // Ini hanya logging - refund actual dihandle manual dari admin
        Log::warning('Midtrans refund/chargeback detected', [
            'order_id' => $order->id,
            'type' => $refundType,
            'amount' => $payload['gross_amount'] ?? 0,
        ]);

        AuditLogService::record(
            'payment_refund_detected',
            'ORDER',
            $order->id,
            ['type' => $refundType, 'amount' => $payload['gross_amount'] ?? 0]
        );
    }

    /**
     * Validasi bahwa payment amount sesuai order total
     */
    private function validateAmount(Order $order, int $paidAmount): bool
    {
        return $paidAmount === $order->total_idr;
    }
}
