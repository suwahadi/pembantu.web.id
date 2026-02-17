<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class ServiceSkill extends Model
{
    public $timestamps = true;

    protected $table = 'service_skills';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function workerSkills(): HasMany
    {
        return $this->hasMany(WorkerSkill::class, 'skill_id');
    }
}
