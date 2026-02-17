<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'label' => 'Administrator'],
            ['name' => 'agency', 'label' => 'Agency Partner'],
            ['name' => 'visitor', 'label' => 'Visitor/Customer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
