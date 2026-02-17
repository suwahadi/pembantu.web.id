<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'transaction_id',
        'status',
        'amount_idr',
        'payment_method',
        'request_payload',
        'last_callback_payload',
        'settled_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'last_callback_payload' => 'array',
        'settled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSettled(): bool
    {
        return $this->status === 'settlement';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['expire', 'cancel', 'deny', 'chargeback']);
    }
}
