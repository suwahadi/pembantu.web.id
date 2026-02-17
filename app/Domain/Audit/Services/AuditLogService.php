<?php

namespace App\Domain\Audit\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Record audit log untuk action tertentu
     */
    public static function record(
        string $action,
        string $modelType,
        int $modelId,
        ?array $beforeData = null,
        ?array $afterData = null
    ): AuditLog {
        return AuditLog::record(
            action: $action,
            modelType: $modelType,
            modelId: $modelId,
            actorUserId: Auth::id(),
            actorType: self::getActorType(),
            beforeData: $beforeData,
            afterData: $afterData,
        );
    }

    /**
     * Get actor type dari role user
     */
    private static function getActorType(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        if ($user->hasRole('admin')) return 'admin';
        if ($user->hasRole('agency')) return 'agency';
        if ($user->hasRole('visitor')) return 'visitor';

        return null;
    }

    /**
     * Get audit logs untuk model
     */
    public static function getForModel(string $modelType, int $modelId)
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
