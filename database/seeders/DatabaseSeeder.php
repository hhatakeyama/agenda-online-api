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
            OrganizationsSeeder::class,
            CompaniesSeeder::class,
            CompanyDaysOfWeekSeeder::class,
            UsersSeeder::class,
            ClientsSeeder::class,
            ServiceCategoriesSeeder::class,
            ServicesSeeder::class,
            EmployeeServicesSeeder::class,
            CompanyEmployeesSeeder::class,
            CompanyServicesSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,
            SchedulesSeeder::class,
            ScheduleItemsSeeder::class,
            
        ]);
    }
}
