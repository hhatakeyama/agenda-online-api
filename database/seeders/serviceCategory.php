<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class serviceCategory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('service_categories')->insert([
            'name' => 'service category 1',
            'organizationId' => '1',
            'status' => true,
        ]);
    }
}
