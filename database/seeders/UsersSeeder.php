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
                'name' => 'Super Admin',
                'occupation' => null,
                'email' => 'super@super.com',
                'password' => Hash::make('super'),
                'picture' => '',
                'type' => 's',
                'organization_id' => null,
            ],
            1 => [
                'name' => 'Admin',
                'occupation' => null,
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => null,
            ],
            2 => [
                'name' => 'Gerente',
                'occupation' => null,
                'email' => 'gerente@gerente.com',
                'password' => Hash::make('gerente'),
                'picture' => '',
                'type' => 'g',
                'organization_id' => 1,
            ],
            3 => [
                'name' => 'Jeffreyson Barbeiro',
                'occupation' => 'Barbeiro',
                'email' => 'funcionario1@skedyou.com',
                'password' => Hash::make('funcionario'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => 1,
            ],
            4 => [
                'name' => 'Periclino Cabeleireiro',
                'occupation' => 'Cabelereiro Leilo',
                'email' => 'funcionario2@skedyou.com',
                'password' => Hash::make('funcionario'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => 1,
            ],
            5 => [
                'name' => 'Raneron Barbeiro',
                'occupation' => 'Barbeiro',
                'email' => 'funcionario3@skedyou.com',
                'password' => Hash::make('funcionario'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => 1,
            ],
            6 => [
                'name' => 'Cabeleireira Leila',
                'occupation' => 'Cabelereira',
                'email' => 'funcionario4@skedyou.com',
                'password' => Hash::make('funcionario'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => 2,
            ],
            7 => [
                'name' => 'Mani Pedicure',
                'occupation' => 'Manicure Pedicure',
                'email' => 'funcionario5@skedyou.com',
                'password' => Hash::make('funcionario'),
                'picture' => '',
                'type' => 'f',
                'organization_id' => 2,
            ],
        ]);
    }
}
