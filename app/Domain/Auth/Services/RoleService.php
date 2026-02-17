<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

final class RoleService
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Check if user has a specific role
     */
    public function userHasRole(int $userId, string $roleCode): bool
    {
        $cacheKey = "user_role:{$userId}:{$roleCode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $roleCode) {
            return DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $userId)
                ->where('roles.code', $roleCode)
                ->exists();
        });
    }

    /**
     * Get all roles for a user
     */
    public function getUserRoles(int $userId): array
    {
        $cacheKey = "user_roles:{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $userId)
                ->pluck('roles.code')
                ->toArray();
        });
    }

    /**
     * Clear cache for user roles
     */
    public function clearCache(int $userId): void
    {
        Cache::forget("user_roles:{$userId}");
        
        foreach (['ADMIN', 'AGENCY', 'VISITOR'] as $role) {
            Cache::forget("user_role:{$userId}:{$role}");
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(int $userId, string $roleCode): void
    {
        $role = DB::table('roles')->where('code', $roleCode)->first();
        
        if (!$role) {
            throw new \Exception("Role {$roleCode} not found.");
        }

        DB::transaction(function () use ($userId, $role) {
            DB::table('user_roles')->updateOrInsert(
                ['user_id' => $userId, 'role_id' => $role->id],
                ['created_at' => now()]
            );

            $this->clearCache($userId);
        });
    }

    /**
     * Remove role from user
     */
    public function removeRole(int $userId, string $roleCode): void
    {
        $role = DB::table('roles')->where('code', $roleCode)->first();
        
        if (!$role) {
            throw new \Exception("Role {$roleCode} not found.");
        }

        DB::transaction(function () use ($userId, $role) {
            DB::table('user_roles')
                ->where('user_id', $userId)
                ->where('role_id', $role->id)
                ->delete();

            $this->clearCache($userId);
        });
    }
}
