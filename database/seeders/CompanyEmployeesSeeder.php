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
                'id' => 1,
                'company_id' => 1,
                'employee_id' => 2,
            ],
            1 => [
                'id' => 2,
                'company_id' => 1,
                'employee_id' => 3,
            ],
        ]);
    }
}
