<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedule_items')->delete();
        
        DB::table('schedule_items')->insert([
            0 => [
                'schedule_id' => 1,
                'employee_id' => 2,
                'service_id' => 2,
                'start_time' => '10:00',
                'end_time' => '10:30',
                'price' => 50.00,
                'duration' => '00:30:00'
            ],          
            1 => [
                'schedule_id' => 2,
                'employee_id' => 2,
                'service_id' => 2,
                'start_time' => '15:00',
                'end_time' => '16:00',
                'price' => 250.00,
                'duration' => '01:00:00'
            ],          
        ]);
    }
}
