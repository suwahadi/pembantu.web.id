<?php

namespace App\Domain\Event\Services;

use App\Models\OrderEvent;
use Illuminate\Support\Facades\Auth;

class OrderEventService
{
    /**
     * Record status change event
     */
    public function recordStatusChange(
        int $orderId,
        string $statusFrom,
        string $statusTo,
        ?int $actorUserId = null,
        ?string $description = null
    ): OrderEvent {
        return OrderEvent::record(
            orderId: $orderId,
            eventType: 'status_change',
            statusFrom: $statusFrom,
            statusTo: $statusTo,
            actorUserId: $actorUserId ?? Auth::id(),
            actorType: $this->getActorType(),
            description: $description,
        );
    }

    /**
     * Record generic event
     */
    public function record(
        int $orderId,
        string $eventType,
        ?string $description = null,
        ?array $metadata = null,
        ?int $actorUserId = null
    ): OrderEvent {
        return OrderEvent::record(
            orderId: $orderId,
            eventType: $eventType,
            actorUserId: $actorUserId ?? Auth::id(),
            actorType: $this->getActorType(),
            description: $description,
            metadata: $metadata,
        );
    }

    /**
     * Get actor type dari role user yang logged in
     */
    private function getActorType(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        if ($user->hasRole('admin')) return 'admin';
        if ($user->hasRole('agency')) return 'agency';
        if ($user->hasRole('visitor')) return 'visitor';

        return null;
    }

    /**
     * Get timeline untuk order
     */
    public function getTimeline(int $orderId)
    {
        return OrderEvent::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
