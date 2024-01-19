<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->delete();

        \DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => 'admin',
                'occupation' => null,
                'email' => 'teste@teste.com',
                'password' => Hash::make('pass'),
                'type' => 's',
                'organization_id' => 1,
            ],
            1 => [
                'id' => 2,
                'name' => 'renan camillo',
                'occupation' => 'barbeiro',
                'email' => 'rcamillo12@gmail.com',
                'password' => Hash::make('pass'),
                'type' => 'f',
                'organization_id' => 1,
            ]
        ]);
    }
}
