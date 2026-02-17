<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function skills(): HasMany
    {
        return $this->hasMany(ServiceSkill::class, 'category_id');
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'category_id');
    }
}
