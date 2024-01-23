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
                'id' => 1,
                'name' => 'admin',
                'email' => 'rcamillo122@gmail.com',
                'password' => Hash::make('pass'),
            ],
            1 => [
                'id' => 2,
                'name' => 'renan camillo',
                'email' => 'rcamillo12@gmail.com',
                'password' => Hash::make('pass'),
            ],
        ]);
    }
}
