<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, HasMany};

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'visitor_user_id',
        'agency_id',
        'worker_id',
        'category_id',
        'contract_id',
        'status',
        'start_date',
        'end_date',
        'subtotal_idr',
        'platform_fee_idr',
        'total_idr',
        'currency',
        'notes',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
            }
        });
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_user_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function escrow(): HasOne
    {
        return $this->hasOne(EscrowHold::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class);
    }
}
