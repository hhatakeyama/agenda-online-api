<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('company_employees')->delete();

        DB::table('company_employees')->insert([
            0 => [
                'company_id' => 1,
                'employee_id' => 4,
            ],
            1 => [
                'company_id' => 1,
                'employee_id' => 5,
            ],
            2 => [
                'company_id' => 1,
                'employee_id' => 6,
            ],
            3 => [
                'company_id' => 2,
                'employee_id' => 7,
            ],
            4 => [
                'company_id' => 2,
                'employee_id' => 8,
            ],
            5 => [
                'company_id' => 3,
                'employee_id' => 7,
            ],
            6 => [
                'company_id' => 3,
                'employee_id' => 8,
            ],
        ]);
    }
}
