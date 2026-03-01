<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Worker;
use App\Models\WorkerServiceArea;
use Illuminate\Database\Seeder;

class WorkerServiceAreaSeeder extends Seeder
{
    public function run(): void
    {
        $locations = Location::all();
        if ($locations->isEmpty()) {
            $this->command->warn('No locations found. Run LocationsSeeder first.');
            return;
        }

        $workers = Worker::all();

        foreach ($workers as $index => $worker) {
            if (WorkerServiceArea::where('worker_id', $worker->id)->exists()) {
                continue;
            }

            $location = $locations[$index % $locations->count()];

            WorkerServiceArea::create([
                'worker_id' => $worker->id,
                'location_id' => $location->id,
                'radius_km' => 10,
                'is_primary' => true,
                'additional_fee_idr' => 0,
                'notes' => '',
                'is_active' => true,
            ]);
        }
    }
}
