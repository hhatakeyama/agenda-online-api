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
                'registeredName' => 'Barbearia Modelo Ltda',
                'tradingName' => 'Barbearia Modelo',
                'cnpj' => '35.799.851/0001-20',
                'slug' => 'barbearia-modelo',
                'status' => true,
            ],
            1 => [
                'registeredName' => 'Cabeleireira Ltda',
                'tradingName' => 'Cabeleireira',
                'cnpj' => '81.851.894/0001-25',
                'slug' => 'cabeleireira-ltda',
                'status' => true,
            ],
        ]);
    }
}
