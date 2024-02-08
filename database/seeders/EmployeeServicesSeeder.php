<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employee_services')->delete();

        DB::table('employee_services')->insert([
            0 => [
                'employee_id' => 4,
                'service_id' => 1,
            ],
            1 => [
                'employee_id' => 4,
                'service_id' => 2,
            ],
            2 => [
                'employee_id' => 5,
                'service_id' => 2,
            ],
            3 => [
                'employee_id' => 6,
                'service_id' => 3,
            ],
            4 => [
                'employee_id' => 7,
                'service_id' => 4,
            ],
            5 => [
                'employee_id' => 8,
                'service_id' => 5,
            ],
            6 => [
                'employee_id' => 8,
                'service_id' => 6,
            ]
        ]);
    }
}
