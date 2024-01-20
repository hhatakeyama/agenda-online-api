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
            CitiesSeeder::class,
            CompaniesSeeder::class,
            CompanyServicesSeeder::class,
            EmployeeServicesSeeder::class,
            OrganizationsSeeder::class,
            ServiceCategoriesSeeder::class,
            ServicesSeeder::class,
            StatesSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
