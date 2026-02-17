<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencyUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'agency');
        })->get();

        if ($agencyUsers->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role agency!');
            return;
        }

        foreach ($agencyUsers as $user) {
            Agency::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'description' => 'Agensi penyedia jasa profesional',
                'phone' => $user->phone,
                'verification_status' => 'verified',
                'verified_at' => now(),
            ]);
        }

        $this->command->info('✓ ' . $agencyUsers->count() . ' agensi berhasil dibuat');
    }
}
