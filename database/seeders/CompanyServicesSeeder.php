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
                'id' => 1,
                'company_id' => 1,
                'service_id' => 1,
                'price' => 20.50,
                'duration' => "00:30",
                'description' => "Teste",
                'send_email' => true,
                'send_sms' => true,
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'status' => true,
            ],
            1 => [
                'id' => 2,
                'company_id' => 1,
                'service_id' => 2,
                'price' => 20.50,
                'duration' => "00:30",
                'description' => "Teste",
                'send_email' => true,
                'send_sms' => true,
                'email_message' => 'Teste',
                'sms_message' => 'Teste',
                'status' => true,
            ]
        ]);
    }
}
