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
            CitysSeeder::class,
            CompanySeeder::class,
            EmployeeServicesSeeder::class,
            OrganizationSeeder::class,
            ServiceCategorySeeder::class,
            ServicesSeeder::class,
            StatesSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
