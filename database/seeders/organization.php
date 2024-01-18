<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class organization extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('organizations')->delete();

        \DB::table('organizations')->insert([
            'registeredName' => 'Organization 1',
            'tradingName' => 'Organization 1',
            'cnpj' => '8.8.8.8',
            'slug' => 'organization1',
            'status' => true,
        ]);
    }
}
