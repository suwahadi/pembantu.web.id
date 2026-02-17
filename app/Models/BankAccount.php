<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'bank_code',
        'bank_name',
        'account_no',
        'account_name',
        'verified_status',
        'verified_at',
        'rejection_reason',
        'is_primary',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_status === 'verified';
    }
}
