<?php

namespace App\Jobs;

use App\Domain\Escrow\Services\EscrowService;
use App\Domain\Payout\Services\PayoutService;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Auto-release escrow setelah dispute window lewat tanpa ada dispute aktif
 * Job ini dijalankan oleh scheduler secara berkala (misal tiap 15 menit)
 */
class ReleaseEscrowJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public function handle(EscrowService $escrowService, PayoutService $payoutService): void
    {
        $disputeWindowHours = (int) config('order.dispute_window_hours', 24);
        $deadline = now()->subHours($disputeWindowHours);

        // Cari orders yang completed lebih dari dispute window
        $completedOrders = Order::query()
            ->where('status', 'completed')
            ->where('updated_at', '<=', $deadline)
            ->limit(100)
            ->get(['id', 'agency_id', 'total_idr', 'visitor_user_id']);

        foreach ($completedOrders as $order) {
            DB::transaction(function () use ($order, $escrowService, $payoutService) {
                // Lock order untuk prevent race condition
                $orderLocked = Order::lockForUpdate()->find($order->id);
                if (!$orderLocked || $orderLocked->status !== 'completed') {
                    return;
                }

                // Check if ada dispute aktif
                $hasActiveDispute = DB::table('disputes')
                    ->where('order_id', $order->id)
                    ->whereIn('status', ['open', 'investigating'])
                    ->lockForUpdate()
                    ->exists();

                if ($hasActiveDispute) {
                    return; // Skip jika ada dispute aktif
                }

                // Get escrow
                $escrow = DB::table('escrow_holds')
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->first();

                if (!$escrow || $escrow->status !== 'hold') {
                    return; // Escrow sudah tidak dalam status hold
                }

                // Release escrow (ledger entry)
                $escrowService->releaseHold($order->id, 'Auto release setelah lama dispute window');

                // Get agency primary bank account
                $agency = DB::table('agencies')
                    ->where('id', $order->agency_id)
                    ->lockForUpdate()
                    ->first();

                $bankAccountId = $agency?->primary_bank_account_id;

                // Queue payout manual
                $payoutService->queuePayout(
                    (int) $order->id,
                    (int) $order->total_idr,
                    (int) $bankAccountId
                );

                // Update order status
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'status' => 'payout_pending',
                        'updated_at' => now(),
                    ]);

                // Record event
                DB::table('order_events')->insert([
                    'order_id' => $order->id,
                    'type' => 'ESCROW_AUTO_RELEASED',
                    'message' => 'Dana escrow dilepas otomatis untuk agency setelah lama dispute window.',
                    'payload' => json_encode([
                        'amount_idr' => $order->total_idr,
                        'released_at' => now(),
                    ]),
                    'created_at' => now(),
                ]);
            });
        }
    }
}
