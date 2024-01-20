<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->delete();

        DB::table('companies')->insert([
            0 => [
                'id' => 1,
                'name' => "sjrp",
                'address' => "doutor preciliano pinto",
                'district' => "boa vista",
                'cep' => "15025-080",
                'city_id' => 3824,
                'state' => "sp",
                'thumb' => "https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.riopreto.sp.gov.br%2Fportal%2Fnoticias%2",
                'email' => 'sjrp@organization.com',
                'organization_id' => 1,
                'phone' => "(17) 3235-1234",
                'mobilePhone' => "(17) 98235-1234",
                'socialMedia' => '{"facebook": "https://facebook.com/", "twitter": "https://twitter.com/", "instagram": "https://instagram.com/"}',
            ],
            1 => [
                'id' => 2,
                'name' => "sjrp2",
                'address' => "santana do parnaiba",
                'district' => "eldorado",
                'cep' => "15025900",
                'city_id' => 3824,
                'state' => "sp",
                'thumb' => "https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.riopreto.sp.gov.br%2Fportal%2Fnoticias%2",
                'email' => 'sjrp@organization.com',
                'organization_id' => 1,
                'phone' => "(17) 3235-1234",
                'mobilePhone' => "(17) 98235-1234",
                'socialMedia' => '{"facebook": "https://facebook.com/", "twitter": "https://twitter.com/", "instagram": "https://instagram.com/"}',
            ]
        ]);
    }
}
