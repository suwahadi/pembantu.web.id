<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin Pembantu',
            'email' => 'admin@pembantu.web.id',
            'password' => Hash::make('admin@pembantu.web.id'),
            'phone' => '081234567890',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Visitor Users (Pencari Jasa)
        $visitor1 = User::create([
            'name' => 'Anwar Abdullah',
            'email' => 'anwar@example.com',
            'password' => Hash::make('anwar@example.com'),
            'phone' => '082112345678',
            'email_verified_at' => now(),
        ]);
        $visitor1->assignRole('visitor');

        $visitor2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('siti@example.com'),
            'phone' => '083145678901',
            'email_verified_at' => now(),
        ]);
        $visitor2->assignRole('visitor');

        $visitor3 = User::create([
            'name' => 'Ahmad Ridho',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('ahmad@example.com'),
            'phone' => '081998765432',
            'email_verified_at' => now(),
        ]);
        $visitor3->assignRole('visitor');

        // Agency Users (Agensi)
        $agency1 = User::create([
            'name' => 'PT Jasa Profesional',
            'email' => 'agency1@example.com',
            'password' => Hash::make('agency1@example.com'),
            'phone' => '081512345678',
            'email_verified_at' => now(),
        ]);
        $agency1->assignRole('agency');

        $agency2 = User::create([
            'name' => 'CV Tenaga Kerja Indonesia',
            'email' => 'agency2@example.com',
            'password' => Hash::make('agency2@example.com'),
            'phone' => '082623456789',
            'email_verified_at' => now(),
        ]);
        $agency2->assignRole('agency');

        // Worker Users (Pekerja) - They are users with the "visitor" or "agency" role who are also workers
        $worker1User = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@example.com',
            'password' => Hash::make('andi@example.com'),
            'phone' => '089123456789',
            'email_verified_at' => now(),
        ]);
        $worker1User->assignRole('visitor'); // Workers can be visitors too

        $worker2User = User::create([
            'name' => 'Lisa Kusuma',
            'email' => 'lisa@example.com',
            'password' => Hash::make('lisa@example.com'),
            'phone' => '088234567890',
            'email_verified_at' => now(),
        ]);
        $worker2User->assignRole('visitor');

        $worker3User = User::create([
            'name' => 'Roni Hermawan',
            'email' => 'roni@example.com',
            'password' => Hash::make('roni@example.com'),
            'phone' => '087345678901',
            'email_verified_at' => now(),
        ]);
        $worker3User->assignRole('visitor');

        $worker4User = User::create([
            'name' => 'Maya Rafsanjani',
            'email' => 'maya@example.com',
            'password' => Hash::make('maya@example.com'),
            'phone' => '085456789012',
            'email_verified_at' => now(),
        ]);
        $worker4User->assignRole('visitor');
    }
}
