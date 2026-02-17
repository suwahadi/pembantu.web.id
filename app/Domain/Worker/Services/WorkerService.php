<?php

namespace App\Domain\Worker\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

final class WorkerService
{
    public function create(int $agencyId, array $data): object
    {
        return DB::transaction(function () use ($agencyId, $data) {
            $id = DB::table('workers')->insertGetId([
                'agency_id' => $agencyId,
                'category_id' => (int)$data['category_id'],
                'location_id' => $data['location_id'] ? (int)$data['location_id'] : null,
                'name' => trim($data['name']),
                'bio' => trim($data['bio'] ?? ''),
                'skills' => trim($data['skills'] ?? ''),
                'default_scheme' => $data['default_scheme'] ?? 'BULANAN',
                'min_price_idr' => (int)$data['min_price_idr'],
                'rank_score' => 0,
                'is_active' => (int)($data['is_active'] ?? 1),
                'photo_path' => $data['photo_path'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return DB::table('workers')->where('id', $id)->first();
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

            DB::table('workers')->where('id', $workerId)->update([
                'category_id' => (int)$data['category_id'],
                'location_id' => $data['location_id'] ? (int)$data['location_id'] : null,
                'name' => trim($data['name']),
                'bio' => trim($data['bio'] ?? ''),
                'skills' => trim($data['skills'] ?? ''),
                'default_scheme' => $data['default_scheme'] ?? 'BULANAN',
                'min_price_idr' => (int)$data['min_price_idr'],
                'photo_path' => $data['photo_path'] ?? $row->photo_path,
                'updated_at' => now(),
            ]);

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

            DB::table('workers')->where('id', $workerId)->update([
                'photo_path' => null,
                'updated_at' => now(),
            ]);
        });
    }
}
