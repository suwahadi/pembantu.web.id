<?php

namespace App\Domain\Worker\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class WorkerPublicIdService
{
    public function generateCandidate(int $length = 8): string
    {
        $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $out = '';
        for ($i=0; $i<$length; $i++) {
            $out .= $pool[random_int(0, strlen($pool)-1)];
        }
        return $out;
    }

    public function generateUnique(int $length = 8, int $maxRetry = 25): string
    {
        for ($i=0; $i<$maxRetry; $i++) {
            $code = $this->generateCandidate($length);
            if (!DB::table('workers')->where('public_id', $code)->exists()) {
                return $code;
            }
        }
        throw new RuntimeException('Gagal menghasilkan public_id unik. Coba naikkan length.');
    }

    public function insertWorkerWithUniquePublicId(callable $insertFn, int $length = 8, int $maxRetry = 25)
    {
        for ($i=0; $i<$maxRetry; $i++) {
            $publicId = $this->generateCandidate($length);

            try {
                return $insertFn($publicId);
            } catch (QueryException $e) {
                if (str_contains($e->getMessage(), 'Duplicate') || (int)($e->errorInfo[1] ?? 0) === 1062) {
                    continue;
                }
                throw $e;
            }
        }
        throw new RuntimeException('Gagal insert worker karena public_id selalu duplicate. Coba naikkan length.');
    }
}
