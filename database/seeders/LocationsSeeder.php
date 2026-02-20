<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'district' => ''],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => ''],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Barat', 'district' => ''],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Timur', 'district' => ''],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'district' => ''],
            ['province' => 'Jawa Barat', 'city' => 'Bogor', 'district' => ''],
            ['province' => 'Jawa Barat', 'city' => 'Depok', 'district' => ''],
            ['province' => 'Banten', 'city' => 'Tangerang', 'district' => ''],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                [
                    'province' => $location['province'],
                    'city' => $location['city'],
                    'district' => $location['district'],
                ],
                $location
            );
        }
    }
}
