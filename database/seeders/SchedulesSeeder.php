<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedules')->delete();
        
        DB::table('schedules')->insert([
            0 => [
                'company_id' => 1,
                'client_id' => 2,
                'date' => '2024-01-23 10:00:00'
            ],
            1 => [
                'company_id' => 1,
                'client_id' => 1,
                'date' => "2024-01-23 15:00:00",
            ]            
        ]);
    }
}
