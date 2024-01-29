<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                'date' => '2024-01-23 10:00:00',
                'confirmed_hash' => Hash::make('1'),
            ],
            1 => [
                'company_id' => 1,
                'client_id' => 1,
                'date' => "2024-01-24 15:00:00",
                'confirmed_hash' => Hash::make('2'),
            ]            
        ]);
    }
}
