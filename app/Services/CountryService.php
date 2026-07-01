<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Country;

class CountryService
{
    /**
     * Mencari data negara dari REST Countries API dan menyimpannya ke Database
     */
    public function getAndSaveCountryInfo($countryName)
    {
        // Panggil API REST Countries
        $url = "https://restcountries.com/v3.1/name/" . urlencode($countryName) . "?fullText=true";
        
        $response = Http::withoutVerifying() 
            ->timeout(30) 
            ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64)') 
            ->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            // PENGAMANAN: Cek dulu apakah datanya benar-benar ada dan bentuknya array
            if (!empty($responseData) && isset($responseData[0])) {
                
                $data = $responseData[0]; // Sekarang aman untuk dipanggil

                // Ekstrak data yang dibutuhkan
                $code = $data['cca2'] ?? null; 
                $region = $data['region'] ?? 'Unknown';

                // Mengambil kode mata uang pertama (dinamis)
                $currencies = $data['currencies'] ?? [];
                $currencyCode = !empty($currencies) ? array_key_first($currencies) : 'USD';

                // Simpan atau update langsung ke tabel countries di database kamu
                $country = Country::updateOrCreate(
                    ['name' => $countryName],
                    [
                        'code' => $code,
                        'currency_code' => $currencyCode,
                        'region' => $region
                    ]
                );

                return $country;
            }
        }

        // Return null jika API gagal atau datanya kosong
        return null;
    }
}