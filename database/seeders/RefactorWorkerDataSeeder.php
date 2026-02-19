<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefactorWorkerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Migrate existing data to new structure
        $this->migrateWorkerSkills();
        $this->migrateWorkerServiceAreas();
        $this->migrateWorkerServicePricings();
        
        $this->command->info('Worker data refactoring completed successfully!');
    }

    private function migrateWorkerSkills()
    {
        // Set first skill as primary for each worker
        \DB::table('worker_skills')->get()->groupBy('worker_id')->each(function ($skills, $workerId) {
            if ($skills->isNotEmpty()) {
                $firstSkillId = $skills->first()->id;
                \DB::table('worker_skills')
                    ->where('id', $firstSkillId)
                    ->update(['is_primary' => true]);
            }
        });

        // Set sort order based on creation time
        \DB::table('worker_skills')->get()->groupBy('worker_id')->each(function ($skills, $workerId) {
            $skills->sortBy('created_at')->values()->each(function ($skill, $index) {
                \DB::table('worker_skills')
                    ->where('id', $skill->id)
                    ->update(['sort_order' => $index]);
            });
        });
    }

    private function migrateWorkerServiceAreas()
    {
        // Set first area as primary for each worker
        \DB::table('worker_service_areas')->get()->groupBy('worker_id')->each(function ($areas, $workerId) {
            if ($areas->isNotEmpty()) {
                $firstAreaId = $areas->first()->id;
                \DB::table('worker_service_areas')
                    ->where('id', $firstAreaId)
                    ->update(['is_primary' => true]);
            }
        });
    }

    private function migrateWorkerServicePricings()
    {
        // Set first pricing as default for each worker
        \DB::table('worker_service_pricings')->get()->groupBy('worker_id')->each(function ($pricings, $workerId) {
            if ($pricings->isNotEmpty()) {
                $firstPricingId = $pricings->first()->id;
                \DB::table('worker_service_pricings')
                    ->where('id', $firstPricingId)
                    ->update(['is_default' => true]);
            }
        });

        // Set sort order based on pricing type priority
        $priorityOrder = ['hourly' => 0, 'daily' => 1, 'weekly' => 2, 'monthly' => 3, 'project' => 4];
        \DB::table('worker_service_pricings')->get()->groupBy('worker_id')->each(function ($pricings, $workerId) use ($priorityOrder) {
            $sortedPricings = $pricings->sortBy(function ($pricing) use ($priorityOrder) {
                return $priorityOrder[$pricing->pricing_type] ?? 999;
            })->values();
            
            $sortedPricings->each(function ($pricing, $index) {
                \DB::table('worker_service_pricings')
                    ->where('id', $pricing->id)
                    ->update(['sort_order' => $index]);
            });
        });
    }
}
