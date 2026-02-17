<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Contract extends Model
{
    protected $fillable = [
        'contract_no',
        'order_id',
        'start_date',
        'end_date',
        'job_scope',
        'location_address',
        'terms_conditions',
        'visitor_signed',
        'visitor_signed_at',
        'agency_signed',
        'agency_signed_at',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'visitor_signed_at' => 'datetime',
        'agency_signed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bothSigned(): bool
    {
        return $this->visitor_signed && $this->agency_signed;
    }
}
