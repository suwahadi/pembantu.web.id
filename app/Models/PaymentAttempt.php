<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentAttempt extends Model
{
    use HasFactory;

    protected $table = 'payment_attempts';

    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'transaction_id',
        'amount_idr',
        'status',
        'raw_payload',
        'callback_received_at',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'callback_received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
