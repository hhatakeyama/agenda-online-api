<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_services')->delete();

        DB::table('company_services')->insert([
            0 => [
                'company_id' => 1,
                'service_id' => 1,
                'price' => 15.00,
                'duration' => '00:30',
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
            1 => [
                'company_id' => 1,
                'service_id' => 2,
                'price' => 10.00,
                'duration' => '00:30',
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
            2 => [
                'company_id' => 1,
                'service_id' => 3,
                'price' => 20.50,
                'duration' => "00:30",
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
            3 => [
                'company_id' => 2,
                'service_id' => 4,
                'price' => 200.50,
                'duration' => "01:30",
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
            4 => [
                'company_id' => 2,
                'service_id' => 5,
                'price' => 120.50,
                'duration' => "00:30",
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
            5 => [
                'company_id' => 2,
                'service_id' => 6,
                'price' => 60.00,
                'duration' => '00:45',
                'description' => "Teste",
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'send_email' => 1,
                'send_sms' => 1,
                'status' => 1,
            ],
        ]);
    }
}
