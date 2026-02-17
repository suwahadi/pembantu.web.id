<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WalletLedger extends Model
{
    protected $table = 'wallet_ledgers';

    protected $fillable = [
        'entry_key',
        'debit_account',
        'credit_account',
        'amount_idr',
        'ref_type',
        'ref_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public static function findByEntryKey(string $entryKey): ?self
    {
        return self::where('entry_key', $entryKey)->first();
    }

    public static function existsByEntryKey(string $entryKey): bool
    {
        return self::where('entry_key', $entryKey)->exists();
    }
}
