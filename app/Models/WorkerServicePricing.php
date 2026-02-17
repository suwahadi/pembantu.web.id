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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
