<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'ART / PRT', 'description' => 'Asisten Rumah Tangga / Pembantu Rumah Tangga', 'icon' => 'mop', 'sort_order' => 1],
            ['name' => 'Babysitter', 'description' => 'Perawatan Anak dan Bayi', 'icon' => 'baby', 'sort_order' => 2],
            ['name' => 'Perawat Lansia', 'description' => 'Perawatan dan Pendampingan Lansia', 'icon' => 'heartpulse', 'sort_order' => 3],
            ['name' => 'Tukang Kebun', 'description' => 'Layanan Berkebun dan Perawatan Taman', 'icon' => 'leaf', 'sort_order' => 4],
            ['name' => 'Sopir', 'description' => 'Jasa Pengiriman dan Pengemudi', 'icon' => 'car', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
