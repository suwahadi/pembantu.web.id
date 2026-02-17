<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, BelongsToMany};

class User extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'primary_bank_account_id',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function assignRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && !$this->hasRole($role)) {
            $this->roles()->attach($roleModel->id);
        }
    }

    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    public function agency(): HasOne
    {
        return $this->hasOne(Agency::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->morphMany(BankAccount::class, 'owner');
    }

    public function primaryBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'primary_bank_account_id');
    }

    public function ordersAsVisitor(): HasMany
    {
        return $this->hasMany(Order::class, 'visitor_user_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'opened_by_user_id');
    }

    public function auditLogsAsActor(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    public function auditLogsAsResolver(): HasMany
    {
        return $this->hasMany(Dispute::class, 'resolved_by_user_id');
    }
}
