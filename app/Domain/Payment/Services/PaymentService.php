<?php

namespace App\Domain\Payment\Services;

use App\Models\{Order, Payment};
use App\Domain\Shared\Statuses\{PaymentStatus, OrderStatus};
use App\Domain\Shared\Support\Idempotency;
use App\Domain\Ledger\Services\LedgerService;
use App\Domain\Escrow\Services\EscrowService;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private LedgerService $ledgerService,
        private EscrowService $escrowService,
        private OrderEventService $eventService,
        private AuditLogService $auditService,
    ) {}

    /**
     * Create payment record untuk order
     * Dipanggil saat checkout dimulai
     */
    public function initiatePayment(string $orderId, string $midtransOrderId, int $amountIdr, array $requestPayload = []): Payment
    {
        return DB::transaction(function () use ($orderId, $midtransOrderId, $amountIdr, $requestPayload) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ($order->status !== OrderStatus::PENDING_PAYMENT) {
                throw new \RuntimeException("Order tidak dalam status pending_payment");
            }

            return Payment::firstOrCreate(
                ['order_id' => $orderId],
                [
                    'midtrans_order_id' => $midtransOrderId,
                    'status' => PaymentStatus::INITIATED,
                    'amount_idr' => $amountIdr,
                    'request_payload' => $requestPayload,
                ]
            );
        });
    }

    /**
     * Handle Midtrans callback - settlement
     * Harus idempoten dengan entry_key unique
     */
    public function handlePaymentSettlement(
        string $midtransOrderId,
        string $transactionId,
        array $callbackPayload
    ): Payment {
        return DB::transaction(function () use ($midtransOrderId, $transactionId, $callbackPayload) {
            $payment = Payment::lockForUpdate()
                ->where('midtrans_order_id', $midtransOrderId)
                ->firstOrFail();

            $order = $payment->order()->lockForUpdate()->first();

            // Check if already settled
            if ($payment->status === PaymentStatus::SETTLEMENT) {
                return $payment;
            }

            // Update payment
            $payment->update([
                'status' => PaymentStatus::SETTLEMENT,
                'transaction_id' => $transactionId,
                'last_callback_payload' => $callbackPayload,
                'settled_at' => now(),
            ]);

            // Create escrow hold
            $this->escrowService->createHold($order->id, $payment->amount_idr);

            // Create ledger entry dengan idempotency
            $entryKey = Idempotency::paymentSettlementKey($order->id, $transactionId);
            $this->ledgerService->createEntry(
                entryKey: $entryKey,
                debitAccount: 'customer_' . $order->visitor_user_id,
                creditAccount: 'escrow_hold',
                amountIdr: $payment->amount_idr,
                refType: 'payment',
                refId: $payment->id,
                description: "Pembayaran order {$order->code}",
            );

            // Update order status
            $order->update(['status' => OrderStatus::PAID_ESCROW]);

            // Record events
            $this->eventService->recordStatusChange($order->id, OrderStatus::PENDING_PAYMENT, OrderStatus::PAID_ESCROW);
            $this->auditService->record('payment_settled', 'ORDER', $order->id);

            return $payment;
        });
    }

    /**
     * Handle payment failure/expiration
     */
    public function handlePaymentFailure(
        string $midtransOrderId,
        string $status,
        array $callbackPayload
    ): Payment {
        return DB::transaction(function () use ($midtransOrderId, $status, $callbackPayload) {
            $payment = Payment::lockForUpdate()
                ->where('midtrans_order_id', $midtransOrderId)
                ->firstOrFail();

            $order = $payment->order()->lockForUpdate()->first();

            // Update payment status
            $payment->update([
                'status' => $status,
                'last_callback_payload' => $callbackPayload,
            ]);

            // Cancel order jika belum settled
            if ($order->status === OrderStatus::PENDING_PAYMENT) {
                $order->update([
                    'status' => OrderStatus::CANCELLED,
                    'cancelled_at' => now(),
                ]);

                $this->eventService->recordStatusChange($order->id, OrderStatus::PENDING_PAYMENT, OrderStatus::CANCELLED);
                $this->auditService->record('payment_failed', 'ORDER', $order->id);
            }

            return $payment;
        });
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(int $orderId): ?Payment
    {
        return Payment::where('order_id', $orderId)->first();
    }
}
