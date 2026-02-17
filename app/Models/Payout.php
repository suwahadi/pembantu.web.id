<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'order_id',
        'agency_id',
        'bank_account_id',
        'amount_idr',
        'status',
        'proof_file_path',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }
}
