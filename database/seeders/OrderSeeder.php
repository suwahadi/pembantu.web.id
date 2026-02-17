<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Worker;
use App\Models\Agency;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visitors = User::whereHas('roles', function ($query) {
            $query->where('name', 'visitor');
        })->get();
        
        $workers = Worker::all();
        $agency = Agency::first();

        if ($visitors->isEmpty() || $workers->isEmpty() || !$agency) {
            $this->command->warn('Tidak ada visitor, worker, atau agency!');
            return;
        }

        $statuses = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'];
        
        $count = 0;
        foreach ($visitors as $visitor) {
            for ($i = 0; $i < 3; $i++) {
                $worker = $workers->random();
                $platformFeePercent = 10; // 10% platform fee
                $subtotal = rand(150000, 500000);
                $platformFee = intval($subtotal * ($platformFeePercent / 100));
                $total = $subtotal + $platformFee;
                $status = $statuses[array_rand($statuses)];
                
                // Generate unique order code
                $code = 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999);
                
                $startDate = Carbon::now()->subDays(rand(1, 30));
                $endDate = $status === 'completed' ? $startDate->copy()->addDays(rand(1, 7)) : null;
                
                Order::create([
                    'code' => $code,
                    'visitor_user_id' => $visitor->id,
                    'agency_id' => $agency->id,
                    'worker_id' => $worker->id,
                    'category_id' => $worker->category_id,
                    'status' => $status,
                    'start_date' => $startDate->toDate(),
                    'end_date' => $endDate ? $endDate->toDate() : null,
                    'subtotal_idr' => $subtotal,
                    'platform_fee_idr' => $platformFee,
                    'total_idr' => $total,
                    'notes' => 'Permintaan layanan dari ' . $visitor->name,
                    'completed_at' => $status === 'completed' ? $endDate : null,
                    'cancelled_at' => $status === 'cancelled' ? now() : null,
                ]);
                $count++;
            }
        }

        $this->command->info('✓ ' . $count . ' pesanan dummy berhasil dibuat');
    }
}
