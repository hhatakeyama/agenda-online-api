<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('service_categories')->delete();
        
        \DB::table('service_categories')->insert([
            0 => [
                'name' => 'service category 1',
                'organization_id' => 1,
                'status' => true,
            ],
            1 => [
                'name' => 'service category 2',
                'organization_id' => 1,
                'status' => true,
            ]            
        ]);
    }
}
