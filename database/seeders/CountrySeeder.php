<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Daftar negara raksasa untuk simulasi Supply Chain Logistik
        $countries = [
            ['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe'],
            ['name' => 'Australia', 'code' => 'AU', 'currency_code' => 'AUD', 'region' => 'Oceania'],
            ['name' => 'Indonesia', 'code' => 'ID', 'currency_code' => 'IDR', 'region' => 'Asia'],
            ['name' => 'China', 'code' => 'CN', 'currency_code' => 'CNY', 'region' => 'Asia'],
            ['name' => 'United States', 'code' => 'US', 'currency_code' => 'USD', 'region' => 'Americas'],
            ['name' => 'Japan', 'code' => 'JP', 'currency_code' => 'JPY', 'region' => 'Asia'],
            ['name' => 'India', 'code' => 'IN', 'currency_code' => 'INR', 'region' => 'Asia'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'currency_code' => 'GBP', 'region' => 'Europe'],
            ['name' => 'Brazil', 'code' => 'BR', 'currency_code' => 'BRL', 'region' => 'Americas'],
            ['name' => 'South Africa', 'code' => 'ZA', 'currency_code' => 'ZAR', 'region' => 'Africa'],
            ['name' => 'Singapore', 'code' => 'SG', 'currency_code' => 'SGD', 'region' => 'Asia'],
            ['name' => 'Netherlands', 'code' => 'NL', 'currency_code' => 'EUR', 'region' => 'Europe'],
        ];

        // Looping untuk memasukkan semua data di atas ke tabel countries
        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']], // Cek agar tidak ada data double
                $country
            );
        }
    }
}