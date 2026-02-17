<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Worker;
use App\Models\ServiceCategory;
use App\Models\Location;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ServiceCategory::all();
        $locations = Location::all();
        
        // Get or create a default agency for workers
        $agency = Agency::first();
        if (!$agency) {
            $this->command->warn('Tidak ada agency! Jalankan AgencySeeder terlebih dahulu.');
            return;
        }

        if ($categories->isEmpty() || $locations->isEmpty()) {
            $this->command->warn('ServiceCategoriesSeeder atau LocationsSeeder belum dijalankan!');
            return;
        }

        $workerNames = [
            'Andi Wijaya',
            'Lisa Kusuma', 
            'Roni Hermawan',
            'Maya Rafsanjani',
            'Siti Nurhaliza',
            'Bambang Sutrisno',
            'Dewi Lestari',
            'Ahmad Ridho'
        ];

        // Create workers
        foreach ($workerNames as $name) {
            $category = $categories->random();
            $location = $locations->random();

            $worker = Worker::create([
                'agency_id' => $agency->id,
                'category_id' => $category->id,
                'name' => $name,
                'bio' => 'Tenaga profesional berpengalaman di bidang ' . $category->name,
                'location_id' => $location->id,
                'phone' => '08' . rand(10000000, 99999999),
                'verification_status' => 'verified',
                'verified_at' => now(),
                'experience_years' => rand(1, 15),
                'rating' => rand(35, 50) / 10,
                'total_reviews' => rand(5, 100),
                'total_completed_orders' => rand(5, 200),
                'is_available' => true,
            ]);
        }

        // Create pricing for workers if the pricings table exists
        $workers = Worker::all();
        foreach ($workers as $worker) {
            if (method_exists($worker, 'pricings') && $worker->pricings()->count() === 0) {
                $worker->pricings()->create([
                    'pricing_type' => 'daily',
                    'description' => 'Layanan standar dengan durasi 1 hari',
                    'price_idr' => rand(150000, 500000),
                    'min_duration' => 1,
                    'max_duration' => 30,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✓ ' . count($workerNames) . ' pekerja berhasil dibuat dengan pricings');
    }
}
