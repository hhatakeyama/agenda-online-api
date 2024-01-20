<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->delete();
        
        DB::table('services')->insert([
            0 => [
                'name' => 'service 1',
                'description' => 'description 1',
                'price' => 15.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 1,
                'organization_id' => 1,
                'status' => true,
            ],
            1 => [
                'name' => 'service 2',
                'description' => 'description 2',
                'price' => 10.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 1,
                'organization_id' => 1,
                'status' => true,
            ]            
        ]);
    }
}
