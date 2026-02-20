<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Worker;
use App\Models\Agency;
use App\Models\ServiceCategory;
use App\Models\ServiceSkill;
use App\Models\Location;
use App\Models\WorkerSkill;
use App\Models\WorkerServiceArea;
use App\Models\WorkerServicePricing;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProperWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing worker data first (in correct order due to foreign keys)
        DB::table('orders')->delete();
        DB::table('worker_skills')->delete();
        DB::table('worker_service_areas')->delete();
        DB::table('worker_service_pricings')->delete();
        DB::table('workers')->delete();

        $this->command->info('Creating 10 proper Indonesian workers...');

        // Get existing data
        $agencies = Agency::all();
        $categories = ServiceCategory::all();
        $skills = ServiceSkill::all();
        $locations = Location::all();

        if ($agencies->isEmpty() || $categories->isEmpty() || $skills->isEmpty() || $locations->isEmpty()) {
            $this->command->error('Required data not found. Please run other seeders first.');
            return;
        }

        // Indonesian worker profiles
        $workers = [
            [
                'name' => 'Mifta Raisa',
                'gender' => 'female',
                'birth_date' => '2001-03-15',
                'phone' => '081234567891',
                'bio' => 'Pengalaman 3 tahun sebagai pembantu rumah tangga. Terampil dalam memasak masakan Indonesia dan membersihkan rumah. Ramah dan dapat diandalkan.',
                'photo_path' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'experience_years' => 3,
                'rating' => 4.5,
                'total_reviews' => 12,
                'total_completed_orders' => 28,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(6),
            ],
            [
                'name' => 'Ahmad Fadli Rahman',
                'gender' => 'male',
                'birth_date' => '2000-07-22',
                'phone' => '082345678902',
                'bio' => 'Supir berpengalaman 4 tahun, menguasai area Jabodetabek. Bersih dan rapi dalam menjaga kendaraan. Memiliki SIM A aktif.',
                'photo_path' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'experience_years' => 4,
                'rating' => 4.8,
                'total_reviews' => 18,
                'total_completed_orders' => 45,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(8),
            ],
            [
                'name' => 'Dewi Lestari',
                'gender' => 'female',
                'birth_date' => '2002-11-08',
                'phone' => '083456789013',
                'bio' => 'Baby sitter profesional, pengalaman merawat anak usia 0-5 tahun. Sabar dan telaten dalam mengurus anak. Bersertifikat first aid.',
                'photo_path' => 'https://randomuser.me/api/portraits/women/28.jpg',
                'experience_years' => 2,
                'rating' => 4.9,
                'total_reviews' => 25,
                'total_completed_orders' => 38,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(4),
            ],
            [
                'name' => 'Budi Santoso',
                'gender' => 'male',
                'birth_date' => '2001-09-30',
                'phone' => '084567890124',
                'bio' => 'Tukang kebun profesional, ahli dalam merawat tanaman hias dan sayuran. Pengalaman 5 tahun di berbagai perumahan.',
                'photo_path' => 'https://randomuser.me/api/portraits/men/67.jpg',
                'experience_years' => 5,
                'rating' => 4.6,
                'total_reviews' => 15,
                'total_completed_orders' => 32,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(10),
            ],
            [
                'name' => 'Rina Amelia',
                'gender' => 'female',
                'birth_date' => '2002-04-18',
                'phone' => '085678901235',
                'bio' => 'Pengalaman 2 tahun sebagai pembantu rumah tangga dan baby sitter. Multitalent, dapat memasak, membersihkan, dan merawat anak.',
                'photo_path' => 'https://randomuser.me/api/portraits/women/65.jpg',
                'experience_years' => 2,
                'rating' => 4.3,
                'total_reviews' => 8,
                'total_completed_orders' => 19,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(3),
            ],
            [
                'name' => 'Muhammad Rizki',
                'gender' => 'male',
                'birth_date' => '2000-12-25',
                'phone' => '086789012346',
                'bio' => 'Supir pribadi dengan pengalaman 6 tahun. Mengenal baik jalan dalam dan luar kota. Disiplin dan dapat dipercaya.',
                'photo_path' => 'https://randomuser.me/api/portraits/men/41.jpg',
                'experience_years' => 6,
                'rating' => 4.7,
                'total_reviews' => 22,
                'total_completed_orders' => 56,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(12),
            ],
            [
                'name' => 'Fitri Handayani',
                'gender' => 'female',
                'birth_date' => '2003-01-12',
                'phone' => '087890123457',
                'bio' => 'Pembantu rumah tangga muda yang energik dan cepat belajar. Terampil dalam memasak masakan tradisional dan modern.',
                'photo_path' => 'https://randomuser.me/api/portraits/women/33.jpg',
                'experience_years' => 1,
                'rating' => 4.2,
                'total_reviews' => 6,
                'total_completed_orders' => 12,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(2),
            ],
            [
                'name' => 'Andi Wijaya',
                'gender' => 'male',
                'birth_date' => '2001-06-14',
                'phone' => '088901234568',
                'bio' => 'Tukang kebun dan maintenance rumah. Pengalaman 4 tahun dalam perawatan taman dan perbaikan kecil rumah tangga.',
                'photo_path' => 'https://randomuser.me/api/portraits/men/15.jpg',
                'experience_years' => 4,
                'rating' => 4.4,
                'total_reviews' => 11,
                'total_completed_orders' => 26,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(5),
            ],
            [
                'name' => 'Sri Wahyuni',
                'gender' => 'female',
                'birth_date' => '2002-08-07',
                'phone' => '089012345679',
                'bio' => 'Baby sitter senior dengan pengalaman 7 tahun. Ahli dalam mengurus anak berkebutuhan khusus dan mengajar anak usia dini.',
                'photo_path' => 'https://randomuser.me/api/portraits/women/50.jpg',
                'experience_years' => 7,
                'rating' => 4.9,
                'total_reviews' => 35,
                'total_completed_orders' => 72,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(15),
            ],
            [
                'name' => 'Eko Prasetyo',
                'gender' => 'male',
                'birth_date' => '2000-10-19',
                'phone' => '089123456780',
                'bio' => 'Supir dan helper rumah tangga. Pengalaman 3 tahun, dapat mengemudikan mobil manual dan matic. Bantu pekerjaan rumah lainnya.',
                'photo_path' => 'https://randomuser.me/api/portraits/men/59.jpg',
                'experience_years' => 3,
                'rating' => 4.1,
                'total_reviews' => 9,
                'total_completed_orders' => 21,
                'availability_status' => 'available',
                'is_available' => true,
                'is_active' => true,
                'verification_status' => 'verified',
                'verified_at' => Carbon::now()->subMonths(7),
            ],
        ];

        foreach ($workers as $index => $workerData) {
            $this->command->info("Creating worker: {$workerData['name']}");

            // Create worker
            $worker = Worker::create([
                'public_id' => 'WRK' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'agency_id' => $agencies->random()->id,
                'category_id' => $categories->random()->id,
                'name' => $workerData['name'],
                'gender' => $workerData['gender'],
                'birth_date' => $workerData['birth_date'],
                'photo_path' => $workerData['photo_path'],
                'bio' => $workerData['bio'],
                'phone' => $workerData['phone'],
                'verification_status' => $workerData['verification_status'],
                'verified_at' => $workerData['verified_at'],
                'experience_years' => $workerData['experience_years'],
                'rating' => $workerData['rating'],
                'total_reviews' => $workerData['total_reviews'],
                'total_completed_orders' => $workerData['total_completed_orders'],
                'availability_status' => $workerData['availability_status'],
                'is_available' => $workerData['is_available'],
                'is_active' => $workerData['is_active'],
            ]);

            // Add skills (2-4 skills per worker)
            $workerSkills = $skills->random(rand(2, 4));
            foreach ($workerSkills as $index => $skill) {
                WorkerSkill::create([
                    'worker_id' => $worker->id,
                    'skill_id' => $skill->id,
                    'proficiency_level' => collect(['basic', 'intermediate', 'advanced', 'expert'])->random(),
                    'experience_years' => rand(1, $workerData['experience_years']),
                    'is_primary' => $index === 0, // First skill is primary
                    'sort_order' => $index,
                    'notes' => $index === 0 ? 'Skill utama dan paling mahir' : null,
                ]);
            }

            // Add service areas (1-3 areas per worker)
            $workerAreas = $locations->random(rand(1, 3));
            foreach ($workerAreas as $index => $location) {
                WorkerServiceArea::create([
                    'worker_id' => $worker->id,
                    'location_id' => $location->id,
                    'radius_km' => rand(5, 20),
                    'is_primary' => $index === 0, // First area is primary
                    'additional_fee_idr' => $index === 0 ? 0 : rand(10000, 50000),
                    'notes' => $index === 0 ? 'Area layanan utama' : 'Area tambahan',
                    'is_active' => true,
                ]);
            }

            // Add pricing (2-3 pricing options per worker)
            $pricingTypes = ['hourly', 'daily', 'weekly', 'monthly', 'project'];
            $selectedPricingTypes = collect($pricingTypes)->random(rand(2, 3));
            
            foreach ($selectedPricingTypes as $index => $pricingType) {
                $basePrice = $this->getBasePrice($pricingType);
                
                WorkerServicePricing::create([
                    'worker_id' => $worker->id,
                    'pricing_type' => $pricingType,
                    'price_idr' => $basePrice + rand(-10000, 50000),
                    'min_duration' => $pricingType === 'hourly' ? 2 : null,
                    'max_duration' => $pricingType === 'hourly' ? 8 : null,
                    'description' => "Pembayaran {$pricingType} untuk layanan {$workerData['name']}",
                    'is_active' => true,
                    'is_default' => $index === 0, // First pricing is default
                    'sort_order' => $index,
                    'min_order_amount' => $pricingType === 'project' ? rand(500000, 2000000) : 0,
                    'effective_date' => Carbon::now()->subMonths(rand(1, 6)),
                    'expiry_date' => null, // No expiry for now
                ]);
            }
        }

        $this->command->info('✅ Successfully created 10 proper Indonesian workers with complete relationships!');
    }

    private function getBasePrice(string $pricingType): int
    {
        $prices = [
            'hourly' => 25000,
            'daily' => 150000,
            'weekly' => 800000,
            'monthly' => 2500000,
            'project' => 1000000,
        ];

        return $prices[$pricingType] ?? 150000;
    }
}
