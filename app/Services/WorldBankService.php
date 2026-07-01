<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WorldBankService
{
    /**
     * Mengambil data Inflasi dan GDP berdasarkan Kode Negara (contoh: 'DE' untuk Jerman)
     */
    public function getEconomicData($countryCode)
    {
        // CACHING: Data ekonomi tidak berubah setiap hari, kita cache 7 hari (10080 menit)
        return Cache::remember("economy_{$countryCode}", 10080, function () use ($countryCode) {
            
            // FP.CPI.TOTL.ZG adalah kode indikator untuk INFLASI di World Bank
            $inflationUrl = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/FP.CPI.TOTL.ZG?format=json&date=2022:2023";
            
            // NY.GDP.MKTP.CD adalah kode indikator untuk GDP
            $gdpUrl = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.MKTP.CD?format=json&date=2022:2023";

            $inflationResponse = Http::get($inflationUrl);
            $gdpResponse = Http::get($gdpUrl);

            $inflation = null;
            $gdp = null;

            // Parsing JSON dari World Bank (Datanya ada di index ke-1 array)
            if ($inflationResponse->successful() && isset($inflationResponse->json()[1][0])) {
                // Ambil nilai inflasi terbaru yang tidak null
                foreach ($inflationResponse->json()[1] as $data) {
                    if ($data['value'] !== null) {
                        $inflation = $data['value'];
                        break;
                    }
                }
            }

            if ($gdpResponse->successful() && isset($gdpResponse->json()[1][0])) {
                foreach ($gdpResponse->json()[1] as $data) {
                    if ($data['value'] !== null) {
                        $gdp = $data['value'];
                        break;
                    }
                }
            }

            return [
                'inflation' => $inflation ? round($inflation, 2) : 0, // Dibulatkan 2 desimal
                'gdp' => $gdp
            ];
        });
    }
}