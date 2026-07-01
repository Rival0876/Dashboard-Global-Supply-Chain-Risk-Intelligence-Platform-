<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Mengambil data kurs mata uang dengan base USD
     * Menggunakan API publik Open Exchange Rates gratis
     */
    public function getLatestRates($baseCurrency = 'USD')
    {
        // CACHING: Kita simpan data selama 12 jam (720 menit) agar hemat pemanggilan API
        return Cache::remember("exchange_rates_{$baseCurrency}", 720, function () use ($baseCurrency) {
            
            // Endpoint gratis tanpa API key
            $url = "https://open.er-api.com/v6/latest/{$baseCurrency}";
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json()['rates'] ?? null;
            }

            return null;
        });
    }

    /**
     * Menghitung nilai tukar dari USD ke mata uang target
     */
    public function convertUsdTo($targetCurrencyCode)
    {
        $rates = $this->getLatestRates('USD');
        
        if ($rates && isset($rates[$targetCurrencyCode])) {
            return $rates[$targetCurrencyCode];
        }

        return null;
    }
}