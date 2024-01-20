<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            0 => [
                'id' => 1,
                'name' => 'admin',
                'occupation' => null,
                'email' => 'teste@teste.com',
                'password' => Hash::make('pass'),
                'picture' => '',
                'type' => 's',
                'organization_id' => 1,
            ],
            1 => [
                'id' => 2,
                'name' => 'renan camillo',
                'occupation' => 'barbeiro',
                'email' => 'rcamillo12@gmail.com',
                'password' => Hash::make('pass'),
                'picture' => 'https://media-gru2-2.cdn.whatsapp.net/v/t61.24694-24/389829319_1391488048383905_9171357230522176244_n.jpg?ccb=11-4&oh=01_AdQ96BdXqadKoNvVx-zqoBXI2f_rZb-tO7ic3GNBFxrw6A&oe=65B82890&_nc_sid=e6e',
                'type' => 'f',
                'organization_id' => 1,
            ],
            2 => [
                'id' => 3,
                'name' => 'Heitor Hatakeyama',
                'occupation' => 'cabelereiro leilo',
                'email' => 'heitor.suh@gmail.com',
                'password' => Hash::make('funcionario'),
                'picture' => 'https://media-gru2-2.cdn.whatsapp.net/v/t61.24694-24/310925824_553876172881165_7859910326844683692_n.jpg?ccb=11-4&oh=01_AdTCWPStT3qgM1GVT0b57LF9wzZxJTh1nGYNQsG3LjkD8A&oe=65B8E204&_nc_sid=e6ed6c&_nc_cat=102',
                'type' => 'f',
                'organization_id' => 1,
            ]
        ]);
    }
}
