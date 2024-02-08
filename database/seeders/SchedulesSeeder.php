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
                'date' => '2024-08-23',
                'confirmed_hash' => Hash::make('1'),
            ],
            1 => [
                'company_id' => 1,
                'client_id' => 1,
                'date' => "2024-09-24",
                'confirmed_hash' => Hash::make('2'),
            ],
            2 => [
                'company_id' => 1,
                'client_id' => 2,
                'date' => '2024-10-23',
                'confirmed_hash' => Hash::make('3'),
            ],
            3 => [
                'company_id' => 1,
                'client_id' => 1,
                'date' => "2024-11-24",
                'confirmed_hash' => Hash::make('4'),
            ]
        ]);
    }
}
