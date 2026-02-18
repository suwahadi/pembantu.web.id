<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Worker;
use App\Models\ServiceCategory;
use App\Models\Location;
use App\Domain\Worker\Services\WorkerPublicIdService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ids = app(WorkerPublicIdService::class);
        $categories = ServiceCategory::all();
        $locations = Location::all();
        $agency = Agency::where('verification_status', 'verified')->first() ?? Agency::first();

        if (!$agency) {
            $this->command->warn('Agency tidak ditemukan.');
            return;
        }

        // Clean up existing workers to avoid duplicates in this specific re-seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('workers')->truncate();
        DB::table('worker_service_pricings')->truncate();
        DB::table('worker_skills')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'name' => 'Slamet Riyadi',
                'gender' => 'Laki-laki',
                'birth_date' => '1992-05-12',
                'category' => 'sopir',
                'bio' => 'Sopir pribadi dengan pengalaman lebih dari 10 tahun di wilayah Jabodetabek. Memiliki SIM A Aktif, rapi, jujur, dan tidak merokok. Sangat hafal rute jalan tikus Jakarta agar menghindari macet.',
                'skills' => 'Mengemudi Matic/Manual, Perawatan Mesin Ringan, Hafal Rute Jakarta, Defensive Driving',
                'photo' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Siti Aminah',
                'gender' => 'Perempuan',
                'birth_date' => '1995-10-20',
                'category' => 'art-prt',
                'bio' => 'Asisten rumah tangga yang rajin dan telaten. Spesialis masak masakan Jawa dan menjaga kebersihan rumah dengan detail. Sudah terbiasa menggunakan alat pembersih modern.',
                'skills' => 'Memasak, Mencuci Setrika, Deep Cleaning, Merapikan Taman',
                'photo' => 'https://images.unsplash.com/photo-1567532939104-b65432793189?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Budi Santoso',
                'gender' => 'Laki-laki',
                'birth_date' => '1988-03-15',
                'category' => 'tukang-kebun',
                'bio' => 'Tukang kebun profesional yang mengerti perawatan tanaman hias dan rumput. Bisa melakukan pruning, pemupukan, dan pembuatan kolam minimalis.',
                'skills' => 'Landscaping, Pemupukan, Perawatan Rumput, Pemangkasan Pohon',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Nur Hayati',
                'gender' => 'Perempuan',
                'birth_date' => '1998-07-04',
                'category' => 'babysitter',
                'bio' => 'Pengasuh anak (Babysitter) bersertifikat. Sangat sabar, menyukai anak-anak, dan mengerti tentang nutrisi MPASI serta stimulasi tumbuh kembang anak usia dini.',
                'skills' => 'MPASI, Stimulasi Bayi, Pertolongan Pertama (P3K), Memandikan Bayi',
                'photo' => 'https://images.unsplash.com/photo-1594744803329-a584af1dd51a?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Winda Lestari',
                'gender' => 'Perempuan',
                'birth_date' => '1990-12-28',
                'category' => 'perawat-lansia',
                'bio' => 'Perawat lansia dengan latar belakang asisten perawat. Berpengalaman merawat pasien pasca stroke dan diabetes. Sabar dalam mendampingi aktivitas harian lansia.',
                'skills' => 'Cek Tensi/Gula Darah, Pendampingan Lansia, Diet Khusus Pasien, Terapi Gerak Ringan',
                'photo' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Agus Setiawan',
                'gender' => 'Laki-laki',
                'birth_date' => '1994-08-11',
                'category' => 'sopir',
                'bio' => 'Sopir operasional kantor atau pribadi. Jujur, tepat waktu, dan memiliki catatan mengemudi yang bersih. Terbiasa mengantar jemput anak sekolah dengan aman.',
                'skills' => 'SIM A & C, Mengemudi Mobil Mewah, Etika Sopir Profesional, Cek Oli/Radiator',
                'photo' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Sri Wahyuni',
                'gender' => 'Perempuan',
                'birth_date' => '1993-01-30',
                'category' => 'art-prt',
                'bio' => 'Pekerja rumah tangga spesialis urusan dapur dan belanja harian. Mengerti cara mengatur stok logistik rumah tangga agar tidak boros. Jujur dan amanah.',
                'skills' => 'Manajemen Belanja, Masak Nusantara, Kebersihan Dapur, Laundry',
                'photo' => 'https://images.unsplash.com/photo-1554151228-14d9def656e4?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Eko Prasetyo',
                'gender' => 'Laki-laki',
                'birth_date' => '1996-06-22',
                'category' => 'tukang-kebun',
                'bio' => 'Pekerja kebun yang gesit. Terbiasa membersihkan halaman luas, menebang pohon yang membahayakan, dan melakukan pembersihan selokan rutin.',
                'skills' => 'Kebersihan Halaman, Alat Potong Rumput, Pembuangan Sampah Kebun',
                'photo' => 'https://images.unsplash.com/photo-1542909168-82c3e7fdca5c?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Lilik Handayani',
                'gender' => 'Perempuan',
                'birth_date' => '1997-11-09',
                'category' => 'babysitter',
                'bio' => 'Babysitter untuk balita. Menarik secara kepribadian, ceria, dan bisa mengajari anak bernyanyi serta membaca dasar. Bersedia menginap.',
                'skills' => 'Storytelling, Dasar Calistung, Menyiapkan Bekal Sehat, Disiplin Anak',
                'photo' => 'https://images.unsplash.com/photo-1557053910-d9eaba703fc9?q=80&w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Heny Sulastri',
                'gender' => 'Perempuan',
                'birth_date' => '1989-04-17',
                'category' => 'perawat-lansia',
                'bio' => 'Pendamping lansia dengan hati. Mengutamakan kenyamanan psikologis lansia agar tidak merasa kesepian. Bisa diajak diskusi dan membacakan koran/buku.',
                'skills' => 'Komunikasi Empatik, Pendampingan Ibadah, Manajemen Obat, Masak Bubur Halus',
                'photo' => 'https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?q=80&w=200&h=200&fit=crop',
            ],
        ];

        foreach ($data as $item) {
            $catId = DB::table('service_categories')->where('slug', $item['category'])->value('id') ?? $categories->random()->id;
            $locId = $locations->random()->id;
            
            $publicId = $ids->generateUnique();
            
            // Monthly Rate: 1.5M - 3.5M
            $price = rand(15, 35) * 100000;

            $workerId = DB::table('workers')->insertGetId([
                'public_id' => $publicId,
                'agency_id' => $agency->id,
                'category_id' => $catId,
                'name' => $item['name'],
                'gender' => $item['gender'],
                'birth_date' => $item['birth_date'],
                'photo_path' => $item['photo'], // We store full URL for seeder convenience
                'bio' => $item['bio'],
                'skills' => $item['skills'],
                'location_id' => $locId,
                'phone' => '08' . rand(110000000, 199999999),
                'verification_status' => 'verified',
                'verified_at' => now(),
                'experience_years' => rand(2, 12),
                'rating' => rand(40, 50) / 10,
                'total_reviews' => rand(3, 50),
                'total_completed_orders' => rand(10, 100),
                'min_price_idr' => $price,
                'default_scheme' => 'BULANAN',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add to pricing table as well for data consistency
            DB::table('worker_service_pricings')->insert([
                'worker_id' => $workerId,
                'pricing_type' => 'monthly',
                'price_idr' => $price,
                'description' => 'Gaji bulanan standar (Full-time)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ ' . count($data) . ' pekerja real berhasil di-seed.');
    }
}
