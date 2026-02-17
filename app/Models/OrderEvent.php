<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEvent extends Model
{
    protected $table = 'order_events';

    protected $fillable = [
        'order_id',
        'event_type',
        'status_from',
        'status_to',
        'actor_user_id',
        'actor_type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public static function record(
        int $orderId,
        string $eventType,
        ?string $statusFrom = null,
        ?string $statusTo = null,
        ?int $actorUserId = null,
        ?string $actorType = null,
        ?string $description = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'order_id' => $orderId,
            'event_type' => $eventType,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'actor_user_id' => $actorUserId,
            'actor_type' => $actorType,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
