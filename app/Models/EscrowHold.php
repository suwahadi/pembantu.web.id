<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowHold extends Model
{
    protected $table = 'escrow_holds';

    protected $fillable = [
        'order_id',
        'amount_idr',
        'status',
        'held_at',
        'released_at',
        'refunded_at',
    ];

    protected $casts = [
        'held_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isHeld(): bool
    {
        return $this->status === 'hold';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function isRefunded(): bool
    {
        return in_array($this->status, ['refunded', 'partial_refunded']);
    }
}
