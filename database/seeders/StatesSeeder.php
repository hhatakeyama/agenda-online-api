<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('states')->delete();

        DB::table('states')->insert(array (
            0 => 
            array (
                'id' => 'AC',
                'name' => 'Acre',
                'region' => 1,
                'ibge_id' => 12,
            ),
            1 => 
            array (
                'id' => 'AL',
                'name' => 'Alagoas',
                'region' => 2,
                'ibge_id' => 27,
            ),
            2 => 
            array (
                'id' => 'AM',
                'name' => 'Amazonas',
                'region' => 1,
                'ibge_id' => 13,
            ),
            3 => 
            array (
                'id' => 'AP',
                'name' => 'Amapá',
                'region' => 1,
                'ibge_id' => 16,
            ),
            4 => 
            array (
                'id' => 'BA',
                'name' => 'Bahia',
                'region' => 2,
                'ibge_id' => 29,
            ),
            5 => 
            array (
                'id' => 'CE',
                'name' => 'Ceará',
                'region' => 2,
                'ibge_id' => 23,
            ),
            6 => 
            array (
                'id' => 'DF',
                'name' => 'Distrito Federal',
                'region' => 5,
                'ibge_id' => 53,
            ),
            7 => 
            array (
                'id' => 'ES',
                'name' => 'Espírito Santo',
                'region' => 3,
                'ibge_id' => 32,
            ),
            8 => 
            array (
                'id' => 'GO',
                'name' => 'Goiás',
                'region' => 5,
                'ibge_id' => 52,
            ),
            9 => 
            array (
                'id' => 'MA',
                'name' => 'Maranhão',
                'region' => 2,
                'ibge_id' => 21,
            ),
            10 => 
            array (
                'id' => 'MG',
                'name' => 'Minas Gerais',
                'region' => 3,
                'ibge_id' => 31,
            ),
            11 => 
            array (
                'id' => 'MS',
                'name' => 'Mato Grosso do Sul',
                'region' => 5,
                'ibge_id' => 50,
            ),
            12 => 
            array (
                'id' => 'MT',
                'name' => 'Mato Grosso',
                'region' => 5,
                'ibge_id' => 51,
            ),
            13 => 
            array (
                'id' => 'PA',
                'name' => 'Pará',
                'region' => 2,
                'ibge_id' => 15,
            ),
            14 => 
            array (
                'id' => 'PB',
                'name' => 'Paraíba',
                'region' => 2,
                'ibge_id' => 25,
            ),
            15 => 
            array (
                'id' => 'PE',
                'name' => 'Pernambuco',
                'region' => 2,
                'ibge_id' => 26,
            ),
            16 => 
            array (
                'id' => 'PI',
                'name' => 'Piauí',
                'region' => 2,
                'ibge_id' => 22,
            ),
            17 => 
            array (
                'id' => 'PR',
                'name' => 'Paraná',
                'region' => 4,
                'ibge_id' => 41,
            ),
            18 => 
            array (
                'id' => 'RJ',
                'name' => 'Rio de Janeiro',
                'region' => 3,
                'ibge_id' => 33,
            ),
            19 => 
            array (
                'id' => 'RN',
                'name' => 'Rio Grande do Norte',
                'region' => 2,
                'ibge_id' => 24,
            ),
            20 => 
            array (
                'id' => 'RO',
                'name' => 'Rondônia',
                'region' => 1,
                'ibge_id' => 11,
            ),
            21 => 
            array (
                'id' => 'RR',
                'name' => 'Roraima',
                'region' => 1,
                'ibge_id' => 14,
            ),
            22 => 
            array (
                'id' => 'RS',
                'name' => 'Rio Grande do Sul',
                'region' => 4,
                'ibge_id' => 43,
            ),
            23 => 
            array (
                'id' => 'SC',
                'name' => 'Santa Catarina',
                'region' => 4,
                'ibge_id' => 42,
            ),
            24 => 
            array (
                'id' => 'SE',
                'name' => 'Sergipe',
                'region' => 2,
                'ibge_id' => 28,
            ),
            25 => 
            array (
                'id' => 'SP',
                'name' => 'São Paulo',
                'region' => 3,
                'ibge_id' => 35,
            ),
            26 => 
            array (
                'id' => 'TO',
                'name' => 'Tocantins',
                'region' => 1,
                'ibge_id' => 17,
            ),
        ));
    }
}
