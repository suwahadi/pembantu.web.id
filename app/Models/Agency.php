<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

class Agency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'business_license_no',
        'tax_id',
        'phone',
        'address',
        'primary_bank_account_id',
        'verification_status',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->morphMany(BankAccount::class, 'owner');
    }

    public function primaryBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'primary_bank_account_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}
