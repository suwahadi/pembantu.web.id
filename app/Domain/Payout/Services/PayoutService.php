<?php

namespace App\Domain\Payout\Services;

use App\Models\{Order, Payout};
use App\Domain\Shared\Statuses\{PayoutStatus, OrderStatus};
use App\Domain\Ledger\Services\LedgerService;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function __construct(
        private LedgerService $ledgerService,
        private OrderEventService $eventService,
    ) {}

    /**
     * Queue payout saat escrow di-release
     */
    public function queuePayout(int $orderId, int $amountIdr, int $bankAccountId): Payout
    {
        return DB::transaction(function () use ($orderId, $amountIdr, $bankAccountId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            $payout = Payout::create([
                'order_id' => $orderId,
                'agency_id' => $order->agency_id,
                'bank_account_id' => $bankAccountId,
                'amount_idr' => $amountIdr,
                'status' => PayoutStatus::QUEUED,
            ]);

            AuditLogService::record('payout_queued', 'PAYOUT', $payout->id);

            return $payout;
        });
    }

    /**
     * Mark payout processing
     */
    public function markProcessing(int $payoutId): Payout
    {
        return DB::transaction(function () use ($payoutId) {
            $payout = Payout::lockForUpdate()->findOrFail($payoutId);

            if ($payout->status !== PayoutStatus::QUEUED) {
                throw new \RuntimeException("Payout tidak dalam status queued");
            }

            $payout->update(['status' => PayoutStatus::PROCESSING]);
            AuditLogService::record('payout_processing', 'PAYOUT', $payout->id);

            return $payout;
        });
    }

    /**
     * Mark payout sebagai paid dan upload proof
     * Ini mengurangi agency_payable account (ledger)
     */
    public function markPaid(int $payoutId, string $proofFilePath): Payout
    {
        return DB::transaction(function () use ($payoutId, $proofFilePath) {
            $payout = Payout::lockForUpdate()->findOrFail($payoutId);

            if (in_array($payout->status, [PayoutStatus::PAID, PayoutStatus::FAILED])) {
                return $payout;
            }

            $payout->update([
                'status' => PayoutStatus::PAID,
                'proof_file_path' => $proofFilePath,
                'paid_at' => now(),
            ]);

            // Create ledger entry: agency_payable -> cash_bank
            $order = $payout->order;
            $entryKey = \App\Domain\Shared\Support\Idempotency::payoutPaidKey($payout->id);

            $this->ledgerService->createEntry(
                entryKey: $entryKey,
                debitAccount: 'agency_' . $payout->agency_id . '_payable',
                creditAccount: 'cash_bank',
                amountIdr: $payout->amount_idr,
                refType: 'payout',
                refId: $payout->id,
                description: "Pembayaran payout untuk order {$order->code} ke agency",
            );

            // Update order status ke released
            $order->update(['status' => OrderStatus::RELEASED]);
            $this->eventService->recordStatusChange($order->id, OrderStatus::PAYOUT_PENDING, OrderStatus::RELEASED, description: 'Payout dibayarkan');

            AuditLogService::record('payout_paid', 'PAYOUT', $payout->id);

            return $payout;
        });
    }

    /**
     * Mark payout failed
     */
    public function markFailed(int $payoutId, string $reason = ''): Payout
    {
        $payout = Payout::lockForUpdate()->findOrFail($payoutId);

        if ($payout->status === PayoutStatus::PAID) {
            throw new \RuntimeException("Tidak bisa mark failed payout yang sudah paid");
        }

        $payout->update([
            'status' => PayoutStatus::FAILED,
            'notes' => $reason,
        ]);

        AuditLogService::record('payout_failed', 'PAYOUT', $payout->id);

        return $payout;
    }

    /**
     * Get queued payouts
     */
    public function getQueuedPayouts()
    {
        return Payout::where('status', PayoutStatus::QUEUED)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get processing payouts
     */
    public function getProcessingPayouts()
    {
        return Payout::where('status', PayoutStatus::PROCESSING)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get payout untuk order
     */
    public function getOrderPayout(int $orderId): ?Payout
    {
        return Payout::where('order_id', $orderId)->first();
    }
}
