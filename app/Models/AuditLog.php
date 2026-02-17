<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'actor_user_id',
        'actor_type',
        'before_data',
        'after_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    public static function record(
        string $action,
        string $modelType,
        int $modelId,
        ?int $actorUserId = null,
        ?string $actorType = null,
        ?array $beforeData = null,
        ?array $afterData = null
    ): self {
        return self::create([
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'actor_user_id' => $actorUserId,
            'actor_type' => $actorType,
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
