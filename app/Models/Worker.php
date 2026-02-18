<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Worker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'public_id',
        'agency_id',
        'category_id',
        'name',
        'bio',
        'skills',
        'location_id',
        'phone',
        'verification_status',
        'verified_at',
        'rejection_reason',
        'experience_years',
        'rating',
        'total_reviews',
        'total_completed_orders',
        'min_price_idr',
        'default_scheme',
        'is_available',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_available' => 'boolean',
        'rating' => 'float',
        'min_price_idr' => 'integer',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(WorkerDocument::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(WorkerSkill::class);
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(WorkerServicePricing::class);
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(WorkerServiceArea::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}
