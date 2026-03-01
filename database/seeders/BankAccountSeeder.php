<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Get users and agencies for bank accounts
        $visitor1 = User::where('email', 'anwar@example.com')->first();
        $visitor2 = User::where('email', 'siti@example.com')->first();
        $agency1 = Agency::first();
        $agency2 = Agency::skip(1)->first();

        // Create bank accounts for visitors
        if ($visitor1 && !BankAccount::where('owner_type', 'App\Models\User')->where('owner_id', $visitor1->id)->exists()) {
            BankAccount::create([
                'owner_type' => 'App\Models\User',
                'owner_id' => $visitor1->id,
                'bank_code' => 'BCA',
                'bank_name' => 'Bank Central Asia',
                'account_no' => '1234567890',
                'account_name' => $visitor1->name,
                'verified_status' => 'verified',
                'verified_at' => now(),
                'is_primary' => true,
            ]);
        }

        if ($visitor2 && !BankAccount::where('owner_type', 'App\Models\User')->where('owner_id', $visitor2->id)->exists()) {
            BankAccount::create([
                'owner_type' => 'App\Models\User',
                'owner_id' => $visitor2->id,
                'bank_code' => 'MANDIRI',
                'bank_name' => 'Bank Mandiri',
                'account_no' => '0987654321',
                'account_name' => $visitor2->name,
                'verified_status' => 'verified',
                'verified_at' => now(),
                'is_primary' => true,
            ]);
        }

        // Create bank accounts for agencies
        if ($agency1 && !BankAccount::where('owner_type', 'App\Models\Agency')->where('owner_id', $agency1->id)->exists()) {
            BankAccount::create([
                'owner_type' => 'App\Models\Agency',
                'owner_id' => $agency1->id,
                'bank_code' => 'BCA',
                'bank_name' => 'Bank Central Asia',
                'account_no' => '1111111111',
                'account_name' => $agency1->company_name,
                'verified_status' => 'verified',
                'verified_at' => now(),
                'is_primary' => true,
            ]);
        }

        if ($agency2 && !BankAccount::where('owner_type', 'App\Models\Agency')->where('owner_id', $agency2->id)->exists()) {
            BankAccount::create([
                'owner_type' => 'App\Models\Agency',
                'owner_id' => $agency2->id,
                'bank_code' => 'BNI',
                'bank_name' => 'Bank Negara Indonesia',
                'account_no' => '2222222222',
                'account_name' => $agency2->company_name,
                'verified_status' => 'verified',
                'verified_at' => now(),
                'is_primary' => true,
            ]);
        }

        $this->command->info('✓ Bank accounts seeded for testing scenarios');
    }
}
