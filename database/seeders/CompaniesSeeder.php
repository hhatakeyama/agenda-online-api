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
                'name' => "São José do Rio Preto - Doutor Presciliano Pinto",
                'address' => "R. Doutor Presciliano Pinto",
                'district' => "Boa Vista",
                'cep' => "15025-080",
                'city_id' => 3824,
                'state' => "SP",
                'thumb' => "https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.riopreto.sp.gov.br%2Fportal%2Fnoticias%2",
                'email' => 'prescilianopinto@organization.com',
                'organization_id' => 1,
                'phone' => "(17) 3235-1234",
                'mobilePhone' => "(17) 98235-1234",
                'map' => "<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3729.4622511415346!2d-49.39454072396017!3d-20.813032266414133!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94bdad65ed14cb09%3A0x12d68ccfc9aa4bae!2sR.%20Dr.%20Presciliano%20Pinto%20-%20S%C3%A3o%20Jos%C3%A9%20do%20Rio%20Preto%2C%20SP!5e0!3m2!1spt-BR!2sbr!4v1705761524709!5m2!1spt-BR!2sbr\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>",
                'socialMedia' => '{"facebook": "https://facebook.com/", "twitter": "https://twitter.com/", "instagram": "https://instagram.com/"}',
            ],
            1 => [
                'name' => "São José do Rio Preto - Santana do Parnaíba",
                'address' => "R. Santana do Parnaíba",
                'district' => "Eldorado",
                'cep' => "15025900",
                'city_id' => 3824,
                'state' => "SP",
                'thumb' => "https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.riopreto.sp.gov.br%2Fportal%2Fnoticias%2",
                'email' => 'santanaparnaiba@organization.com',
                'organization_id' => 2,
                'phone' => "(17) 3235-1234",
                'mobilePhone' => "(17) 98235-1234",
                'map' => "<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3730.097601497017!2d-49.402576223960956!3d-20.787338965579302!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94bdad023176e0f1%3A0x7695620cde734d1a!2sR.%20Santana%20do%20Parna%C3%ADba%20-%20S%C3%A3o%20Jos%C3%A9%20do%20Rio%20Preto%2C%20SP%2C%2015043-090!5e0!3m2!1spt-BR!2sbr!4v1705761718754!5m2!1spt-BR!2sbr\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>",
                'socialMedia' => '{"facebook": "https://facebook.com/", "twitter": "https://twitter.com/", "instagram": "https://instagram.com/"}',
            ],
            2 => [
                'name' => "São José do Rio Preto - Doutor Presciliano Pinto",
                'address' => "R. Doutor Presciliano Pinto",
                'district' => "Boa Vista",
                'cep' => "15025-080",
                'city_id' => 3824,
                'state' => "SP",
                'thumb' => "https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.riopreto.sp.gov.br%2Fportal%2Fnoticias%2",
                'email' => 'prescilianopinto@organization.com',
                'organization_id' => 2,
                'phone' => "(17) 3235-1234",
                'mobilePhone' => "(17) 98235-1234",
                'map' => "<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3729.4622511415346!2d-49.39454072396017!3d-20.813032266414133!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94bdad65ed14cb09%3A0x12d68ccfc9aa4bae!2sR.%20Dr.%20Presciliano%20Pinto%20-%20S%C3%A3o%20Jos%C3%A9%20do%20Rio%20Preto%2C%20SP!5e0!3m2!1spt-BR!2sbr!4v1705761524709!5m2!1spt-BR!2sbr\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>",
                'socialMedia' => '{"facebook": "https://facebook.com/", "twitter": "https://twitter.com/", "instagram": "https://instagram.com/"}',
            ]
        ]);
    }
}
