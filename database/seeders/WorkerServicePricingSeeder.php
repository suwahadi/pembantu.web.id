<?php

namespace Database\Seeders;

use App\Models\Worker;
use App\Models\WorkerServicePricing;
use Illuminate\Database\Seeder;

class WorkerServicePricingSeeder extends Seeder
{
    public function run(): void
    {
        $workers = Worker::all();
        $priceOptions = [
            2_500_000,
            2_650_000,
            2_700_000,
            2_750_000,
            2_850_000,
            2_900_000,
            2_950_000,
            3_000_000,
            3_100_000,
            3_200_000,
        ];

        foreach ($workers as $index => $worker) {
            $price = $priceOptions[array_rand($priceOptions)];

            WorkerServicePricing::firstOrCreate(
                ['worker_id' => $worker->id],
                [
                    'pricing_type' => 'monthly',
                    'price_idr' => $price,
                    'min_duration' => 1,
                    'max_duration' => 1,
                    'description' => '',
                    'is_active' => true,
                    'is_default' => true,
                    'sort_order' => $index + 1,
                    'min_order_amount' => $price,
                    'effective_date' => now()->subMonth(),
                    'expiry_date' => null,
                ]
            );
        }
    }
}
