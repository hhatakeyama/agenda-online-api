<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clients')->delete();

        DB::table('clients')->insert([
            0 => [
                'name' => 'Heitor Hatakeyama',
                'email' => 'heitor.suh@gmail.com',
                'mobilePhone' => '17991323162',
                'password' => Hash::make('pass'),
            ],
            1 => [
                'name' => 'Renan camillo',
                'email' => 'rcamillo12@gmail.com',
                'mobilePhone' => '17982241332',
                'password' => Hash::make('pass'),
            ],
            2 => [
                'name' => 'Teste',
                'email' => 'teste@teste.com',
                'mobilePhone' => '1791323162',
                'password' => Hash::make('teste'),
            ],
        ]);
    }
}
