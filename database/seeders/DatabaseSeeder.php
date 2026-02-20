<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            ServiceCategoriesSeeder::class,
            ServiceSkillsSeeder::class,
            LocationsSeeder::class,
            UserSeeder::class,
            AgencySeeder::class,
            //WorkerSeeder::class,
            ProperWorkerSeeder::class,
            //OrderSeeder::class,
        ]);
    }
}
