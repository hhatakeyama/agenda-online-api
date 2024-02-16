<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_categories')->delete();
        
        DB::table('service_categories')->insert([
            0 => [
                'name' => 'Categoria Comum',
                'organization_id' => 1,
                'status' => 1,
            ],
            1 => [
                'name' => 'Serviço Premium',
                'organization_id' => 1,
                'status' => 1,
            ],            
            2 => [
                'name' => 'Serviço VIP',
                'organization_id' => 1,
                'status' => 1,
            ],            
            3 => [
                'name' => 'Cortes Comuns',
                'organization_id' => 2,
                'status' => 1,
            ],            
            4 => [
                'name' => 'Serviço de Beleza',
                'organization_id' => 2,
                'status' => 1,
            ]            
        ]);
    }
}
