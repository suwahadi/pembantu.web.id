<?php

namespace App\Domain\Order\Services;

use App\Models\Order;
use App\Domain\Shared\Statuses\OrderStatus;
use App\Domain\Event\Services\OrderEventService;
use App\Domain\Audit\Services\AuditLogService;
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
            $order = Order::create([
                'visitor_user_id' => $visitorUserId,
                'agency_id' => $agencyId,
                'worker_id' => $workerId,
                'category_id' => $categoryId,
                'contract_id' => $contractId,
                'status' => OrderStatus::PENDING_PAYMENT,
                'start_date' => now()->date(),
                'subtotal_idr' => (int) ($totalIdr * 0.95), // 95% untuk service
                'platform_fee_idr' => (int) ($totalIdr * 0.05), // 5% platform fee
                'total_idr' => $totalIdr,
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
     * Get orders untuk worker
     */
    public function getWorkerOrders(int $workerId)
    {
        return Order::where('worker_id', $workerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
