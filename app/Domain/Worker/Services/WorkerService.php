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
                    'name' => trim($data['name']),
                    'bio' => trim($data['bio'] ?? ''),
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
                'name' => trim($data['name']),
                'bio' => trim($data['bio'] ?? ''),
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

        $inserts = array_map(function ($skillId, $index) use ($workerId) {
            return [
                'worker_id' => $workerId,
                'skill_id' => $skillId,
                'proficiency_level' => 'basic',
                'experience_years' => 1,
                'is_primary' => $index === 0, // First skill is primary
                'sort_order' => $index,
                'notes' => $index === 0 ? 'Skill utama' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $skillIds, array_keys($skillIds));

        DB::table('worker_skills')->insert($inserts);
    }

    private function syncPricings(int $workerId, array $pricings): void
    {
        // Simple implementation for now: if pricings provided, replace existing
        if (empty($pricings)) {
            return;
        }

        DB::table('worker_service_pricings')->where('worker_id', $workerId)->delete();

        $inserts = array_map(function ($p, $index) use ($workerId) {
            return [
                'worker_id' => $workerId,
                'pricing_type' => $p['pricing_type'] ?? 'daily',
                'price_idr' => $p['price_idr'] ?? 0,
                'description' => $p['description'] ?? null,
                'is_active' => true,
                'is_default' => $index === 0, // First pricing is default
                'sort_order' => $index,
                'effective_date' => now(),
                'expiry_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $pricings, array_keys($pricings));

        DB::table('worker_service_pricings')->insert($inserts);
    }

    private function syncAreas(int $workerId, array $locationIds): void
    {
        DB::table('worker_service_areas')->where('worker_id', $workerId)->delete();

        if (empty($locationIds)) {
            return;
        }

        $inserts = array_map(function ($locId, $index) use ($workerId) {
            return [
                'worker_id' => $workerId,
                'location_id' => $locId,
                'radius_km' => 10,
                'is_primary' => $index === 0, // First location is primary
                'additional_fee_idr' => 0,
                'notes' => $index === 0 ? 'Area layanan utama' : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $locationIds, array_keys($locationIds));

        DB::table('worker_service_areas')->insert($inserts);
    }
}
