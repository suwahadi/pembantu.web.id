<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeEvidence extends Model
{
    protected $table = 'dispute_evidences';

    protected $fillable = [
        'dispute_id',
        'submitted_by_type',
        'submitted_by_id',
        'file_path',
        'description',
    ];

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }
}
