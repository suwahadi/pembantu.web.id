<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Location extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'province',
        'city',
        'district',
        'postal_code',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function serviceAreas(): HasMany
    {
        return $this->hasMany(WorkerServiceArea::class);
    }
}
