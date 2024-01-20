<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyDaysOfWeekSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('company_days_of_weeks')->delete();

        DB::table('company_days_of_weeks')->insert([
            0 => [
                'id' => 1,
                'day_of_week' => '1',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'start_time_2' => '',
                'end_time_2' => '',
                'start_time_3' => '',
                'end_time_3' => '',
                'start_time_4' => '',
                'end_time_4' => '',
                'company_id' => 1,
            ],
            1 => [
                'id' => 2,
                'day_of_week' => '2',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'start_time_2' => '',
                'end_time_2' => '',
                'start_time_3' => '',
                'end_time_3' => '',
                'start_time_4' => '',
                'end_time_4' => '',
                'company_id' => 1,
            ],
            2 => [
                'id' => 3,
                'day_of_week' => '3',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'start_time_2' => '',
                'end_time_2' => '',
                'start_time_3' => '',
                'end_time_3' => '',
                'start_time_4' => '',
                'end_time_4' => '',
                'company_id' => 1,
            ],
            3 => [
                'id' => 4,
                'day_of_week' => '4',
                'start_time' => '08:00',
                'end_time' => '12:00',
                'start_time_2' => '13:00',
                'end_time_2' => '18:00',
                'start_time_3' => '',
                'end_time_3' => '',
                'start_time_4' => '',
                'end_time_4' => '',
                'company_id' => 1,
            ],
            4 => [
                'id' => 5,
                'day_of_week' => '5',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'start_time_2' => '',
                'end_time_2' => '',
                'start_time_3' => '',
                'end_time_3' => '',
                'start_time_4' => '',
                'end_time_4' => '',
                'company_id' => 1,
            ],
        ]);
    }
}
