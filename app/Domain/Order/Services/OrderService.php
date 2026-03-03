<?php

namespace App\Domain\Order\Services;

use App\Models\{Order, User};
use App\Models\WorkerServicePricing;
use App\Domain\Shared\Statuses\OrderStatus;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private OrderEventService $eventService) {}

    /**
     * Create order dari contract
     */
    public function createFromContract(int $contractId, int $visitorUserId, int $agencyId, int $workerId, int $categoryId, int $totalIdr): Order
    {
        return DB::transaction(function () use ($contractId, $visitorUserId, $agencyId, $workerId, $categoryId, $totalIdr) {
            $contract = DB::table('contracts')->where('id', $contractId)->lockForUpdate()->first();
            if (!$contract) {
                throw new \RuntimeException('Kontrak tidak ditemukan.');
            }

            $contractStart = $contract->start_date ? Carbon::parse($contract->start_date)->toDateString() : now()->toDateString();
            $contractEnd = $contract->end_date ? Carbon::parse($contract->end_date)->toDateString() : null;

            $metadata = [];
            if (!empty($contract->metadata)) {
                $decoded = is_string($contract->metadata) ? json_decode($contract->metadata, true) : $contract->metadata;
                $metadata = is_array($decoded) ? $decoded : [];
            }

            $scheme = strtoupper((string) ($metadata['scheme'] ?? 'BULANAN'));
            $schemeToPricingType = [
                'PER_JAM' => 'hourly',
                'HARIAN' => 'daily',
                'MINGGUAN' => 'weekly',
                'BULANAN' => 'monthly',
            ];
            $pricingType = $schemeToPricingType[$scheme] ?? 'monthly';

            $pricingQuery = WorkerServicePricing::query()
                ->where('worker_id', $workerId)
                ->where('is_active', true)
                ->where('pricing_type', $pricingType)
                ->orderByDesc('is_default')
                ->orderBy('sort_order');

            $pricing = $pricingQuery->first();
            if (!$pricing) {
                $pricing = WorkerServicePricing::query()
                    ->where('worker_id', $workerId)
                    ->where('is_active', true)
                    ->orderByDesc('is_default')
                    ->orderBy('sort_order')
                    ->first();
            }

            $unitCount = 1;
            if ($contractEnd) {
                $start = Carbon::parse($contractStart);
                $end = Carbon::parse($contractEnd);
                $days = max(1, $start->diffInDays($end) + 1);

                if ($pricingType === 'daily') {
                    $unitCount = $days;
                } elseif ($pricingType === 'weekly') {
                    $unitCount = (int) ceil($days / 7);
                } elseif ($pricingType === 'monthly') {
                    $unitCount = max(1, $start->diffInMonths($end) + 1);
                } elseif ($pricingType === 'hourly') {
                    $unitCount = $days * 8;
                } else {
                    $unitCount = 1;
                }
            }

            $computedTotal = 0;
            if ($pricing) {
                $computedTotal = max(
                    (int) ($unitCount * (int) $pricing->price_idr),
                    (int) ($pricing->min_order_amount ?? 0)
                );
            }

            $finalTotal = $computedTotal > 0 ? $computedTotal : $totalIdr;

            $order = Order::create([
                'visitor_user_id' => $visitorUserId,
                'agency_id' => $agencyId,
                'worker_id' => $workerId,
                'category_id' => $categoryId,
                'contract_id' => $contractId,
                'status' => OrderStatus::PENDING_PAYMENT,
                'start_date' => $contractStart,
                'end_date' => $contractEnd,
                'subtotal_idr' => (int) ($finalTotal * 0.95),
                'platform_fee_idr' => (int) ($finalTotal * 0.05),
                'total_idr' => $finalTotal,
            ]);

            // Link back contract to order
            $nextMetadata = array_merge($metadata, [
                'pricing_type' => $pricingType,
                'unit_count' => $unitCount,
                'price_per_unit_idr' => (int) ($pricing->price_idr ?? 0),
                'pricing_id' => (int) ($pricing->id ?? 0),
            ]);

            DB::table('contracts')->where('id', $contractId)->update([
                'order_id' => $order->id,
                'scope_of_work' => $pricingType,
                'total_price_idr' => $finalTotal,
                'platform_fee_idr' => (int) ($finalTotal * 0.05),
                'status' => 'signed_by_visitor',
                'metadata' => json_encode($nextMetadata),
            ]);

            $this->eventService->record(
                $order->id,
                'order_created',
                'Order dibuat dari kontrak',
            );

            AuditLogService::record('order_created', 'ORDER', $order->id);

            return $order;
        });
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $newStatus): Order
    {
        return DB::transaction(function () use ($orderId, $newStatus) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if (!OrderStatus::isValid($newStatus)) {
                throw new \InvalidArgumentException("Invalid status: {$newStatus}");
            }

            $oldStatus = $order->status;
            
            $order->update(['status' => $newStatus]);

            $this->eventService->recordStatusChange($orderId, $oldStatus, $newStatus);
            AuditLogService::record('status_updated', 'ORDER', $orderId, ['old' => $oldStatus], ['new' => $newStatus]);

            return $order;
        });
    }

    /**
     * Mark order sebagai selesai
     */
    public function complete(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ($order->status !== OrderStatus::IN_PROGRESS) {
                throw new \RuntimeException("Order tidak dalam status in_progress");
            }

            $order->update([
                'status' => OrderStatus::COMPLETED,
                'end_date' => now()->date(),
                'completed_at' => now(),
            ]);

            $this->eventService->recordStatusChange($orderId, OrderStatus::IN_PROGRESS, OrderStatus::COMPLETED);
            AuditLogService::record('order_completed', 'ORDER', $orderId);

            return $order;
        });
    }

    /**
     * Cancel order
     */
    public function cancel(int $orderId, string $reason = ''): Order
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if (in_array($order->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED])) {
                throw new \RuntimeException("Order tidak dapat dibatalkan dari status {$order->status}");
            }

            $oldStatus = $order->status;
            $order->update([
                'status' => OrderStatus::CANCELLED,
                'cancelled_at' => now(),
                'notes' => ($order->notes ? $order->notes . "\n" : '') . "Dibatalkan: {$reason}",
            ]);

            $this->eventService->recordStatusChange($orderId, $oldStatus, OrderStatus::CANCELLED, description: $reason);
            AuditLogService::record('order_cancelled', 'ORDER', $orderId);

            return $order;
        });
    }

    /**
     * Get order by code
     */
    public function getByCode(string $code): ?Order
    {
        return Order::where('code', $code)->first();
    }

    /**
     * Get orders untuk user sebagai visitor
     */
    public function getVisitorOrders(int $visitorUserId)
    {
        return Order::where('visitor_user_id', $visitorUserId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders untuk agency
     */
    public function getAgencyOrders(int $agencyId)
    {
        return Order::where('agency_id', $agencyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mulai pekerjaan oleh agency
     */
    public function startJobByAgency(int $orderId, int $agencyId, int $actorUserId): Order
    {
        return DB::transaction(function () use ($orderId, $agencyId, $actorUserId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ((int)$order->agency_id !== $agencyId) {
                throw new \RuntimeException('Akses ditolak.');
            }

            if ($order->status !== OrderStatus::PAID_ESCROW) {
                throw new \RuntimeException('Order harus dalam status paid_escrow untuk mulai pekerjaan.');
            }

            $order->update(['status' => OrderStatus::IN_PROGRESS]);

            $this->eventService->record(
                $orderId,
                'work_started',
                'Pekerjaan dimulai oleh agency (actor: ' . $actorUserId . ')',
            );

            // Send notification after successful commit
            DB::afterCommit(function () use ($orderId, $order) {
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $visitor->notify(new OrderStatusChanged(
                        orderId: $orderId,
                        orderCode: $order->code,
                        newStatus: OrderStatus::IN_PROGRESS,
                        message: 'Pekerjaan Anda telah dimulai oleh agency.'
                    ));
                }
            });

            return $order;
        });
    }

    /**
     * Selesaikan pekerjaan oleh agency
     */
    public function finishJobByAgency(int $orderId, int $agencyId, int $actorUserId): Order
    {
        return DB::transaction(function () use ($orderId, $agencyId, $actorUserId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ((int)$order->agency_id !== $agencyId) {
                throw new \RuntimeException('Akses ditolak.');
            }

            if ($order->status !== OrderStatus::IN_PROGRESS) {
                throw new \RuntimeException('Order harus dalam status in_progress untuk menyelesaikan pekerjaan.');
            }

            $order->update(['status' => 'completed_by_agency']);

            $this->eventService->record(
                $orderId,
                'work_completed_by_agency',
                'Pekerjaan ditandai selesai oleh agency (actor: ' . $actorUserId . ')',
            );

            // Send notification after successful commit
            DB::afterCommit(function () use ($orderId, $order) {
                $visitor = User::find($order->visitor_user_id);
                if ($visitor) {
                    $visitor->notify(new OrderStatusChanged(
                        orderId: $orderId,
                        orderCode: $order->code,
                        newStatus: 'completed_by_agency',
                        message: 'Agency telah menyelesaikan pekerjaan. Silakan konfirmasi jika puas.'
                    ));
                }
            });

            return $order;
        });
    }

    /**
     * Konfirmasi penyelesaian oleh visitor (settle escrow, queue payout)
     */
    public function confirmCompletionByVisitor(int $orderId, int $visitorUserId): void
    {
        DB::transaction(function () use ($orderId, $visitorUserId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ((int)$order->visitor_user_id !== $visitorUserId) {
                throw new \RuntimeException('Akses ditolak.');
            }

            if ($order->status !== 'completed_by_agency') {
                throw new \RuntimeException('Order harus ditandai selesai oleh agency terlebih dahulu.');
            }

            // Update order status ke completed
            $order->update([
                'status' => OrderStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            // Settle escrow - release payout untuk agency
            $payout = DB::table('payouts')->where('order_id', $orderId)->lockForUpdate()->first();
            if ($payout && $payout->status === 'queued') {
                DB::table('payouts')->where('id', $payout->id)->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);
            }

            $this->eventService->record(
                $orderId,
                'order_confirmed_completed',
                'Visitor mengkonfirmasi pekerjaan selesai (actor: ' . $visitorUserId . ')',
            );

            // Send notification to agency after successful commit
            DB::afterCommit(function () use ($orderId, $order) {
                $agency = DB::table('agencies')->where('id', $order->agency_id)->first();
                if ($agency) {
                    // Notify agency owner (get from agency_users or owner_user_id)
                    $agencyOwner = User::find($agency->owner_user_id ?? $agency->primary_owner_user_id);
                    if ($agencyOwner) {
                        $agencyOwner->notify(new OrderStatusChanged(
                            orderId: $orderId,
                            orderCode: $order->code,
                            newStatus: OrderStatus::COMPLETED,
                            message: 'Visitor telah mengkonfirmasi penyelesaian pekerjaan. Payout siap diproses.'
                        ));
                    }
                }
            });
        });
    }
}

