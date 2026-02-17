<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'district' => 'Menteng'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'district' => 'Tanah Abang'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Kebayoran Baru'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Tebet'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Barat', 'district' => 'Palmerah'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Timur', 'district' => 'Kramat Jati'],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'district' => 'Penjaringan'],
            ['province' => 'Jawa Barat', 'city' => 'Bogor', 'district' => 'Bogor Tengah'],
            ['province' => 'Jawa Barat', 'city' => 'Depok', 'district' => 'Depok Pertanian'],
            ['province' => 'Banten', 'city' => 'Tangerang', 'district' => 'Tangerang Pusat'],
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
