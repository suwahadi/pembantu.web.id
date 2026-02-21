<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        Location::truncate();
        
        // Reset auto-increment
        DB::statement('ALTER TABLE locations AUTO_INCREMENT = 1');
        
        $locations = [
            [
                'city' => 'Jakarta Pusat',
                'slug' => 'jakarta-pusat',
                'created_at' => '2026-02-19 15:17:00',
                'updated_at' => '2026-02-19 15:17:00',
            ],
            [
                'city' => 'Jakarta Selatan',
                'slug' => 'jakarta-selatan',
                'created_at' => '2026-02-19 15:17:00',
                'updated_at' => '2026-02-19 15:17:00',
            ],
            [
                'city' => 'Jakarta Barat',
                'slug' => 'jakarta-barat',
                'created_at' => '2026-02-19 15:17:00',
                'updated_at' => '2026-02-19 15:17:00',
            ],
            [
                'city' => 'Jakarta Timur',
                'slug' => 'jakarta-timur',
                'created_at' => '2026-02-19 15:17:01',
                'updated_at' => '2026-02-19 15:17:01',
            ],
            [
                'city' => 'Jakarta Utara',
                'slug' => 'jakarta-utara',
                'created_at' => '2026-02-19 15:17:01',
                'updated_at' => '2026-02-19 15:17:01',
            ],
            [
                'city' => 'Bogor',
                'slug' => 'bogor',
                'created_at' => '2026-02-19 15:17:01',
                'updated_at' => '2026-02-19 15:17:01',
            ],
            [
                'city' => 'Depok',
                'slug' => 'depok',
                'created_at' => '2026-02-19 15:17:01',
                'updated_at' => '2026-02-19 15:17:01',
            ],
            [
                'city' => 'Tangerang',
                'slug' => 'tangerang',
                'created_at' => '2026-02-19 15:17:01',
                'updated_at' => '2026-02-19 15:17:01',
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
