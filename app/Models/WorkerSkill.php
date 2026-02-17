<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerSkill extends Model
{
    protected $fillable = [
        'worker_id',
        'skill_id',
        'proficiency_level',
        'experience_years',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(ServiceSkill::class, 'skill_id');
    }
}
