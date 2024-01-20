<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organizations')->delete();

        DB::table('organizations')->insert([
            0 => [
                'registeredName' => 'Organization 1 Ltda',
                'tradingName' => 'Organization 1',
                'cnpj' => '12.345.678/0001-90',
                'slug' => 'organization-1-ltda',
                'status' => true,
            ],
            1 => [
                'registeredName' => 'Organization 2 Ltda',
                'tradingName' => 'Organization 2',
                'cnpj' => '12.345.678/0001-91',
                'slug' => 'organization-2-ltda',
                'status' => true,
            ],
        ]);
    }
}
