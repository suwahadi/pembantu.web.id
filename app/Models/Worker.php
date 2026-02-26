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
        'location_id',
        'name',
        'gender',
        'birth_date',
        'photo_path',
        'bio',
        'phone',
        'verification_status',
        'verified_at',
        'rejection_reason',
        'experience_years',
        'rating',
        'total_reviews',
        'total_completed_orders',
        'availability_status',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'birth_date' => 'date',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'float',
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

    public function workerSkills(): HasMany
    {
        return $this->hasMany(WorkerSkill::class);
    }

    public function skills()
    {
        return $this->belongsToMany(ServiceSkill::class, 'worker_skills', 'worker_id', 'skill_id')
            ->withPivot(['proficiency_level', 'experience_years', 'is_primary', 'sort_order', 'notes'])
            ->withTimestamps();
    }

    public function primarySkills()
    {
        return $this->belongsToMany(ServiceSkill::class, 'worker_skills', 'worker_id', 'skill_id')
            ->withPivot(['proficiency_level', 'experience_years', 'is_primary', 'sort_order', 'notes'])
            ->wherePivot('is_primary', true)
            ->orderBy('pivot_sort_order')
            ->withTimestamps();
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(WorkerServicePricing::class);
    }

    public function defaultPricing()
    {
        return $this->hasOne(WorkerServicePricing::class)->where('is_default', true);
    }

    public function activePricings()
    {
        return $this->hasMany(WorkerServicePricing::class)->where('is_active', true);
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(WorkerServiceArea::class);
    }

    public function primaryServiceArea()
    {
        return $this->hasOne(WorkerServiceArea::class)->where('is_primary', true);
    }

    public function activeServiceAreas()
    {
        return $this->hasMany(WorkerServiceArea::class)->where('is_active', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getMinPriceAttribute()
    {
        $defaultPricing = $this->defaultPricing;
        if ($defaultPricing) {
            return $defaultPricing->price_idr;
        }
        
        $activePricing = $this->activePricings()->first();
        return $activePricing ? $activePricing->price_idr : 0;
    }

    public function getMinPriceUnitAttribute(): string
    {
        $pricingTypeMap = [
            'hourly' => 'jam',
            'daily' => 'hari',
            'weekly' => 'minggu',
            'monthly' => 'bulan',
            'project' => 'proyek',
        ];

        $defaultPricing = $this->defaultPricing;
        if ($defaultPricing) {
            return $pricingTypeMap[$defaultPricing->pricing_type] ?? 'bulan';
        }

        $activePricing = $this->activePricings()->first();
        if ($activePricing) {
            return $pricingTypeMap[$activePricing->pricing_type] ?? 'bulan';
        }

        return 'bulan';
    }

    public function getPrimaryLocationAttribute()
    {
        $primaryArea = $this->primaryServiceArea;
        return $primaryArea ? $primaryArea->location : null;
    }

    public function getSkillsListAttribute()
    {
        return $this->skills->pluck('name')->implode(', ');
    }

    public function getPrimarySkillsListAttribute()
    {
        return $this->primarySkills->pluck('name')->implode(', ');
    }

    public function addSkill(ServiceSkill $skill, array $attributes = [])
    {
        $attributes = array_merge([
            'proficiency_level' => 'basic',
            'experience_years' => 0,
            'is_primary' => false,
            'sort_order' => $this->workerSkills()->count(),
            'notes' => null,
        ], $attributes);

        return $this->skills()->attach($skill, $attributes);
    }

    public function removeSkill(ServiceSkill $skill)
    {
        return $this->skills()->detach($skill);
    }

    public function setPrimarySkill(ServiceSkill $skill)
    {
        // Reset all skills to non-primary
        $this->workerSkills()->update(['is_primary' => false]);
        
        // Set the specified skill as primary
        return $this->workerSkills()
            ->where('skill_id', $skill->id)
            ->update(['is_primary' => true]);
    }
}
