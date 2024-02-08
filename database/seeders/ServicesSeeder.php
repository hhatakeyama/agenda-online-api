<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->delete();

        DB::table('services')->insert([
            0 => [
                'name' => 'Cabelo',
                'description' => 'Corte de cabelo comum',
                'price' => 15.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 1,
                'organization_id' => 1,
                'can_choose_random' => true,
                'can_choose_employee' => true,
                'can_simultaneous' => true,
                'status' => true,
            ],
            1 => [
                'name' => 'Barba',
                'description' => 'Corte de barba comum',
                'price' => 10.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 1,
                'organization_id' => 1,
                'can_choose_random' => false,
                'can_choose_employee' => false,
                'can_simultaneous' => true,
                'status' => true,
            ],
            2 => [
                'name' => 'Cabelo Premium',
                'description' => 'Corte de cabelo premium',
                'price' => 15.00,
                'duration' => '00:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 2,
                'organization_id' => 1,
                'can_choose_random' => true,
                'can_choose_employee' => true,
                'can_simultaneous' => true,
                'status' => true,
            ],
            3 => [
                'name' => 'Cabelo Comum',
                'description' => 'Corte de cabelo comum',
                'price' => 200.00,
                'duration' => '01:30',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 4,
                'organization_id' => 2,
                'can_choose_random' => false,
                'can_choose_employee' => false,
                'can_simultaneous' => true,
                'status' => true,
            ],
            4 => [
                'name' => 'Mão Premium',
                'description' => '',
                'price' => 120.00,
                'duration' => '01:00',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 5,
                'organization_id' => 2,
                'can_choose_random' => false,
                'can_choose_employee' => false,
                'can_simultaneous' => true,
                'status' => true,
            ],
            5 => [
                'name' => 'Pé Premium',
                'description' => '',
                'price' => 60.00,
                'duration' => '00:45',
                'send_email' => 1,
                'send_sms' => 1,
                'serviceCategory_id' => 5,
                'organization_id' => 2,
                'can_choose_random' => false,
                'can_choose_employee' => false,
                'can_simultaneous' => true,
                'status' => true,
            ]
        ]);
    }
}
