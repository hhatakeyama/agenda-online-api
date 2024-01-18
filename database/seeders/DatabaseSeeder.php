<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            States::class,
            Citys::class,
            Organization::class,
            ServiceCategory::class,            
            ServicesSeeder::class,
            UsersSeeder::class,            
            EmployeeServicesSeeder::class,
        ]);
    }
}
