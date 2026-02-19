<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerServicePricing extends Model
{
    protected $table = 'worker_service_pricings';

    protected $fillable = [
        'worker_id',
        'pricing_type',
        'price_idr',
        'min_duration',
        'max_duration',
        'description',
        'is_active',
        'is_default',
        'sort_order',
        'min_order_amount',
        'effective_date',
        'expiry_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'price_idr' => 'integer',
        'min_duration' => 'integer',
        'max_duration' => 'integer',
        'sort_order' => 'integer',
        'min_order_amount' => 'integer',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
