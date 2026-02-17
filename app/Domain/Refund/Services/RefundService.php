<?php

namespace App\Domain\Refund\Services;

use App\Models\{Order, Refund, User};
use App\Domain\Shared\Statuses\{RefundStatus, OrderStatus};
use App\Domain\Ledger\Services\LedgerService;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use App\Notifications\TransferCompleted;
use Illuminate\Support\Facades\{DB, Auth};

class RefundService
{
    public function __construct(
        private LedgerService $ledgerService,
        private OrderEventService $eventService,
    ) {}

    /**
     * Queue refund - dibuat saat dispute di-resolve dengan refund decision
     */
    public function queueRefund(int $orderId, int $amountIdr, int $bankAccountId, string $reason = ''): Refund
    {
        return DB::transaction(function () use ($orderId, $amountIdr, $bankAccountId, $reason) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            $refund = Refund::create([
                'order_id' => $orderId,
                'payee_type' => 'USER',
                'payee_id' => $order->visitor_user_id,
                'bank_account_id' => $bankAccountId,
                'amount_idr' => $amountIdr,
                'status' => RefundStatus::QUEUED,
                'reason' => $reason,
            ]);

            AuditLogService::record('refund_queued', 'REFUND', $refund->id);

            return $refund;
        });
    }

    /**
     * Update refund status ke processing saat admin mulai process
     */
    public function markProcessing(int $refundId): Refund
    {
        return DB::transaction(function () use ($refundId) {
            $refund = Refund::lockForUpdate()->findOrFail($refundId);

            if ($refund->status !== RefundStatus::QUEUED) {
                throw new \RuntimeException("Refund tidak dalam status queued");
            }

            $refund->update(['status' => RefundStatus::PROCESSING]);
            AuditLogService::record('refund_processing', 'REFUND', $refund->id);

            return $refund;
        });
    }

    /**
     * Mark refund sebagai paid dan upload proof
     * Ini mengurangi customer_refundable account (ledger)
     */
    public function markPaid(int $refundId, string $proofFilePath): Refund
    {
        return DB::transaction(function () use ($refundId, $proofFilePath) {
            $refund = Refund::lockForUpdate()->findOrFail($refundId);

            if (in_array($refund->status, [RefundStatus::PAID, RefundStatus::CANCELLED])) {
                return $refund;
            }

            $refund->update([
                'status' => RefundStatus::PAID,
                'proof_file_path' => $proofFilePath,
                'paid_at' => now(),
            ]);

            // Create ledger entry: customer_refundable -> cash_bank
            $order = $refund->order;
            $entryKey = \App\Domain\Shared\Support\Idempotency::refundPaidKey($refund->id);

            $this->ledgerService->record(
                entryKey: $entryKey,
                debitAccount: 'customer_' . $order->visitor_user_id . '_refundable',
                creditAccount: 'cash_bank',
                amountIdr: $refund->amount_idr,
                refType: 'refund',
                refId: $refund->id,
                note: "Pembayaran refund untuk order {$order->code}",
            );

            // Update order status jika semua refund paid
            if (!$order->refund()->first()) {
                // No more refunds
                $order->update(['status' => OrderStatus::REFUNDED]);
            }

            AuditLogService::record('refund_paid', 'REFUND', $refund->id);

            // Send notification after successful commit
            DB::afterCommit(function () use ($refund, $order) {
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $visitor->notify(new TransferCompleted(
                        type: 'refund',
                        transferId: $refund->id,
                        orderId: $refund->order_id,
                        message: 'Refund Anda telah berhasil diproses dan ditransfer.',
                        amountIdr: $refund->amount_idr
                    ));
                }
            });

            return $refund;
        });
    }

    /**
     * Cancel refund
     */
    public function cancel(int $refundId, string $reason = ''): Refund
    {
        return DB::transaction(function () use ($refundId, $reason) {
            $refund = Refund::lockForUpdate()->findOrFail($refundId);

            if ($refund->status === RefundStatus::PAID) {
                throw new \RuntimeException("Tidak bisa cancel refund yang sudah paid");
            }

            $refund->update([
                'status' => RefundStatus::CANCELLED,
                'notes' => $reason,
            ]);

            AuditLogService::record('refund_cancelled', 'REFUND', $refund->id);

            return $refund;
        });
    }

    /**
     * Get queue refunds
     */
    public function getQueuedRefunds()
    {
        return Refund::where('status', RefundStatus::QUEUED)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get processing refunds
     */
    public function getProcessingRefunds()
    {
        return Refund::where('status', RefundStatus::PROCESSING)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get refund untuk order
     */
    public function getOrderRefund(int $orderId): ?Refund
    {
        return Refund::where('order_id', $orderId)->first();
    }
}
