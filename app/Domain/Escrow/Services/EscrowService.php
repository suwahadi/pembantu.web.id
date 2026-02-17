<?php

namespace App\Domain\Escrow\Services;

use App\Models\{Order, EscrowHold};
use App\Domain\Shared\Statuses\{EscrowStatus, OrderStatus};
use App\Domain\Shared\Support\Idempotency;
use App\Domain\Ledger\Services\LedgerService;
use Illuminate\Support\Facades\DB;

class EscrowService
{
    public function __construct(private LedgerService $ledgerService) {}

    /**
     * Create escrow hold setelah payment settlement
     */
    public function createHold(int $orderId, int $amountIdr): EscrowHold
    {
        return DB::transaction(function () use ($orderId, $amountIdr) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Create atau update escrow
            $escrow = EscrowHold::firstOrCreate(
                ['order_id' => $orderId],
                [
                    'amount_idr' => $amountIdr,
                    'status' => EscrowStatus::HOLD,
                    'held_at' => now(),
                ]
            );

            return $escrow;
        });
    }

    /**
     * Release escrow penuh ke payout
     * Dipanggil ketika order completed dan no dispute window lewat
     */
    public function releaseHold(int $orderId, string $reason = ''): EscrowHold
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $escrow = EscrowHold::lockForUpdate()->where('order_id', $orderId)->firstOrFail();
            $order = $escrow->order()->lockForUpdate()->first();

            if ($escrow->status !== EscrowStatus::HOLD) {
                throw new \RuntimeException("Escrow tidak dalam status hold");
            }

            $escrow->update([
                'status' => EscrowStatus::RELEASED,
                'released_at' => now(),
            ]);

            // Create ledger: escrow -> agency_payable
            $entryKey = Idempotency::escrowHoldKey($orderId);
            $this->ledgerService->record(
                entryKey: $entryKey,
                debitAccount: 'escrow_hold',
                creditAccount: 'agency_' . $order->agency_id . '_payable',
                amountIdr: $escrow->amount_idr,
                refType: 'order',
                refId: $orderId,
                note: "Release escrow untuk order {$order->code}. {$reason}",
            );

            return $escrow;
        });
    }

    /**
     * Refund penuh dari escrow ke customer
     */
    public function refundFull(int $orderId, string $reason = ''): EscrowHold
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $escrow = EscrowHold::lockForUpdate()->where('order_id', $orderId)->firstOrFail();
            $order = $escrow->order()->lockForUpdate()->first();

            if ($escrow->status !== EscrowStatus::HOLD) {
                throw new \RuntimeException("Escrow tidak dalam status hold");
            }

            $escrow->update([
                'status' => EscrowStatus::REFUNDED,
                'refunded_at' => now(),
            ]);

            // Create ledger: escrow -> customer_refundable
            $entryKey = Idempotency::escrowHoldKey($orderId) . ':REFUND';
            $this->ledgerService->record(
                entryKey: $entryKey,
                debitAccount: 'escrow_hold',
                creditAccount: 'customer_' . $order->visitor_user_id . '_refundable',
                amountIdr: $escrow->amount_idr,
                refType: 'order',
                refId: $orderId,
                note: "Refund full escrow untuk order {$order->code}. {$reason}",
            );

            return $escrow;
        });
    }

    /**
     * Refund parsial: sebagian ke customer, sebagian ke agency
     */
    public function refundPartial(int $orderId, int $refundAmountIdr, int $releaseAmountIdr, string $reason = ''): EscrowHold
    {
        return DB::transaction(function () use ($orderId, $refundAmountIdr, $releaseAmountIdr, $reason) {
            $escrow = EscrowHold::lockForUpdate()->where('order_id', $orderId)->firstOrFail();
            $order = $escrow->order()->lockForUpdate()->first();

            if ($escrow->status !== EscrowStatus::HOLD) {
                throw new \RuntimeException("Escrow tidak dalam status hold");
            }

            if (($refundAmountIdr + $releaseAmountIdr) !== $escrow->amount_idr) {
                throw new \RuntimeException("Jumlah refund + release tidak sama dengan escrow amount");
            }

            $escrow->update([
                'status' => EscrowStatus::PARTIAL_RELEASED,
                'released_at' => now(),
                'refunded_at' => now(),
            ]);

            // Ledger: escrow -> customer_refundable
            if ($refundAmountIdr > 0) {
                $entryKeyRefund = Idempotency::escrowHoldKey($orderId) . ':PARTIAL_REFUND';
                $this->ledgerService->createEntry(
                    entryKey: $entryKeyRefund,
                    debitAccount: 'escrow_hold',
                    creditAccount: 'customer_' . $order->visitor_user_id . '_refundable',
                    amountIdr: $refundAmountIdr,
                    refType: 'order',
                    refId: $orderId,
                    description: "Refund partial untuk order {$order->code}. {$reason}",
                );
            }

            // Ledger: escrow -> agency_payable
            if ($releaseAmountIdr > 0) {
                $entryKeyRelease = Idempotency::escrowHoldKey($orderId) . ':PARTIAL_RELEASE';
                $this->ledgerService->createEntry(
                    entryKey: $entryKeyRelease,
                    debitAccount: 'escrow_hold',
                    creditAccount: 'agency_' . $order->agency_id . '_payable',
                    amountIdr: $releaseAmountIdr,
                    refType: 'order',
                    refId: $orderId,
                    description: "Release partial untuk order {$order->code}. {$reason}",
                );
            }

            return $escrow;
        });
    }

    /**
     * Get escrow hold untuk order
     */
    public function getHold(int $orderId): ?EscrowHold
    {
        return EscrowHold::where('order_id', $orderId)->first();
    }
}
