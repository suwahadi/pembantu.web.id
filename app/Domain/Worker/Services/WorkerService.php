<?php

namespace App\Domain\Worker\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class WorkerService
{
    public function create(int $agencyId, array $data): object
    {
        return DB::transaction(function () use ($agencyId, $data) {
            $ids = app(WorkerPublicIdService::class);

            return $ids->insertWorkerWithUniquePublicId(function (string $publicId) use ($agencyId, $data) {
                $workerData = [
                    'public_id' => $publicId,
                    'agency_id' => $agencyId,
                    'category_id' => (int)$data['category_id'],
                    'location_id' => $data['location_id'] ? (int)$data['location_id'] : null,
                    'name' => trim($data['name']),
                    'bio' => trim($data['bio'] ?? ''),
                    'skills' => $this->formatSkillsCache($data['skills'] ?? []),
                    'default_scheme' => $data['default_scheme'] ?? 'BULANAN',
                    'min_price_idr' => (int)$data['min_price_idr'],
                    'is_active' => (int)($data['is_active'] ?? 1),
                    'photo_path' => $data['photo_path'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $id = DB::table('workers')->insertGetId($workerData);

                $this->syncSkills($id, $data['skills'] ?? []);
                $this->syncPricings($id, $data['pricings'] ?? []);
                $this->syncAreas($id, $data['areas'] ?? []);

                return DB::table('workers')->where('id', $id)->first();
            }, length: 8, maxRetry: 25);
        });
    }

    public function update(int $agencyId, int $workerId, array $data): object
    {
        return DB::transaction(function () use ($agencyId, $workerId, $data) {
            $row = DB::table('workers')->where('id', $workerId)->lockForUpdate()->first();
            if (!$row) {
                throw new RuntimeException('Worker tidak ditemukan.');
            }
            if ((int)$row->agency_id !== $agencyId) {
                throw new RuntimeException('Akses ditolak.');
            }

            if (isset($data['photo_path']) && $data['photo_path'] !== $row->photo_path) {
                if ($row->photo_path) {
                    Storage::disk('public')->delete($row->photo_path);
                }
            }

            DB::table('workers')->where('id', $workerId)->update([
                'category_id' => (int)$data['category_id'],
                'location_id' => $data['location_id'] ? (int)$data['location_id'] : null,
                'name' => trim($data['name']),
                'bio' => trim($data['bio'] ?? ''),
                'skills' => $this->formatSkillsCache($data['skills'] ?? []),
                'default_scheme' => $data['default_scheme'] ?? 'BULANAN',
                'min_price_idr' => (int)$data['min_price_idr'],
                'photo_path' => $data['photo_path'] ?? $row->photo_path,
                'updated_at' => now(),
            ]);

            $this->syncSkills($workerId, $data['skills'] ?? []);
            $this->syncPricings($workerId, $data['pricings'] ?? []);
            $this->syncAreas($workerId, $data['areas'] ?? []);

            return DB::table('workers')->where('id', $workerId)->first();
        });
    }

    public function setActive(int $agencyId, int $workerId, bool $active): void
    {
        DB::transaction(function () use ($agencyId, $workerId, $active) {
            $row = DB::table('workers')->where('id', $workerId)->lockForUpdate()->first();
            if (!$row) {
                throw new RuntimeException('Worker tidak ditemukan.');
            }
            if ((int)$row->agency_id !== $agencyId) {
                throw new RuntimeException('Akses ditolak.');
            }

            DB::table('workers')->where('id', $workerId)->update([
                'is_active' => $active ? 1 : 0,
                'updated_at' => now(),
            ]);
        });
    }

    public function deletePhoto(int $agencyId, int $workerId): void
    {
        DB::transaction(function () use ($agencyId, $workerId) {
            $row = DB::table('workers')->where('id', $workerId)->lockForUpdate()->first();
            if (!$row) {
                throw new RuntimeException('Worker tidak ditemukan.');
            }
            if ((int)$row->agency_id !== $agencyId) {
                throw new RuntimeException('Akses ditolak.');
            }

            if ($row->photo_path) {
                Storage::disk('public')->delete($row->photo_path);
            }

            DB::table('workers')->where('id', $workerId)->update([
                'photo_path' => null,
                'updated_at' => now(),
            ]);
        });
    }

    private function formatSkillsCache(array $skillIds): string
    {
        if (empty($skillIds)) {
            return '';
        }

        return DB::table('service_skills')
            ->whereIn('id', $skillIds)
            ->pluck('name')
            ->implode(', ');
    }

    private function syncSkills(int $workerId, array $skillIds): void
    {
        DB::table('worker_skills')->where('worker_id', $workerId)->delete();

        if (empty($skillIds)) {
            return;
        }

        $inserts = array_map(fn($skillId) => [
            'worker_id' => $workerId,
            'skill_id' => $skillId,
            'proficiency_level' => 'basic',
            'created_at' => now(),
            'updated_at' => now(),
        ], $skillIds);

        DB::table('worker_skills')->insert($inserts);
    }

    private function syncPricings(int $workerId, array $pricings): void
    {
        // Simple implementation for now: if pricings provided, replace existing
        if (empty($pricings)) {
            return;
        }

        DB::table('worker_service_pricings')->where('worker_id', $workerId)->delete();

        $inserts = array_map(fn($p) => [
            'worker_id' => $workerId,
            'pricing_type' => $p['pricing_type'] ?? 'daily',
            'price_idr' => $p['price_idr'] ?? 0,
            'description' => $p['description'] ?? null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], $pricings);

        DB::table('worker_service_pricings')->insert($inserts);
    }

    private function syncAreas(int $workerId, array $locationIds): void
    {
        DB::table('worker_service_areas')->where('worker_id', $workerId)->delete();

        if (empty($locationIds)) {
            return;
        }

        $inserts = array_map(fn($locId) => [
            'worker_id' => $workerId,
            'location_id' => $locId,
            'radius_km' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ], $locationIds);

        DB::table('worker_service_areas')->insert($inserts);
    }
}
