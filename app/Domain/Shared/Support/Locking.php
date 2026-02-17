<?php

namespace App\Domain\Shared\Support;

use Illuminate\Database\Eloquent\Model;

class Locking
{
    /**
     * Implementasi row lock untuk operasi kritis
     * Menggunakan SELECT ... FOR UPDATE
     */
    public static function lockForUpdate(Model|\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->lockForUpdate();
    }

    /**
     * Skip locked rows dan ambil yang tersedia
     */
    public static function skipLocked(Model|\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->skipLocked();
    }
}
