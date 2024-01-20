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
                'id' => 1,
                'employee_id' => 1,
                'service_id' => 1,
            ],
            1 => [
                'id' => 2,
                'employee_id' => 1,
                'service_id' => 2,
            ]
        ]);
    }
}
