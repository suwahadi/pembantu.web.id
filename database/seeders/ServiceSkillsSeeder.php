<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{ServiceCategory, ServiceSkill};

class ServiceSkillsSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // ART / PRT
            'ART / PRT' => [
                'Membersihkan Rumah',
                'Mencuci Pakaian',
                'Memasak Makanan',
                'Mengurus Taman',
                'Mengelola Keuangan Rumah',
            ],
            // Babysitter
            'Babysitter' => [
                'Perawatan Bayi',
                'Pendampingan Belajar',
                'Kesehatan Anak',
                'Nutrisi Anak',
                'Pertumbuhan Perkembangan',
            ],
            // Perawat Lansia
            'Perawat Lansia' => [
                'Kesehatan Umum',
                'Penanganan Penyakit Kronis',
                'Mobilitas & Rehabilitasi',
                'Nutrisi Lansia',
                'Kesehatan Mental Lansia',
            ],
            // Tukang Kebun
            'Tukang Kebun' => [
                'Penanaman Tumbuhan',
                'Perawatan Tanaman',
                'Pemangkasan',
                'Sistem Irigasi',
                'Desain Taman',
            ],
            // Sopir
            'Sopir' => [
                'Berkendara Aman',
                'Pengetahuan Rute',
                'Perbaikan Mesin Dasar',
                'Perawatan Kendaraan',
                'Protokol Keselamatan',
            ],
        ];

        foreach ($skills as $categoryName => $skillNames) {
            $category = ServiceCategory::where('name', $categoryName)->first();
            if ($category) {
                foreach ($skillNames as $index => $skillName) {
                    ServiceSkill::firstOrCreate(
                        ['category_id' => $category->id, 'name' => $skillName],
                        [
                            'description' => null,
                            'sort_order' => $index + 1,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
