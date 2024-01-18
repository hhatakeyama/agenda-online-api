<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class servicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('services')->delete();
        
        \DB::table('services')->insert([
            0 => [
                'name' => 'service 1',
                'description' => 'description 1',
                'price' => 15.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategoryId' => 1,
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
                'serviceCategoryId' => 1,
                'organization_id' => 1,
                'status' => true,
            ]            
        ]);
    }
}
