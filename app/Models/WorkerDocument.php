<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerDocument extends Model
{
    protected $fillable = [
        'worker_id',
        'document_type',
        'document_no',
        'file_path',
        'issued_at',
        'expired_at',
        'verification_status',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expired_at' => 'date',
        'verified_at' => 'datetime',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
}
