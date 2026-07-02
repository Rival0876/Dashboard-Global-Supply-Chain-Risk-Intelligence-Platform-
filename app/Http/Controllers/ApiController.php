<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\WeatherService;
use App\Services\WorldBankService;
use App\Services\NewsService;
use App\Services\RiskAnalysisService;
use App\Services\ExchangeRateService;

class ApiController extends Controller
{
    // 1. GET /api/countries -> Ambil daftar negara (Untuk Dropdown Frontend)
    public function getCountries()
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    // 2. GET /api/risk -> Ambil skor risiko (Gabungan Cuaca, Ekonomi, Berita)
    public function getRisk(Request $request)
    {
        $countryCode = $request->query('code'); // Frontend akan mengirim: /api/risk?code=ID

        if (!$countryCode) {
            return response()->json(['error' => 'Kode negara wajib dikirim'], 400);
        }

        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return response()->json(['error' => 'Negara tidak ditemukan'], 404);
        }

        // Koordinat sederhana untuk Open-Meteo (idealnya disimpan di DB, tapi ini cukup untuk simulasi)
        $coords = [
            'DE' => ['lat' => 51.16, 'lng' => 10.45], // Germany
            'AU' => ['lat' => -25.27, 'lng' => 133.77], // Australia
            'ID' => ['lat' => -0.78, 'lng' => 113.92], // Indonesia
            'CN' => ['lat' => 35.86, 'lng' => 104.19], // China
            'US' => ['lat' => 37.09, 'lng' => -95.71], // USA
            'JP' => ['lat' => 36.20, 'lng' => 138.25], // Japan
        ];

        // Jika negara tidak ada di daftar koordinat atas, set default ke 0
        $lat = $coords[$countryCode]['lat'] ?? 0;
        $lng = $coords[$countryCode]['lng'] ?? 0;

        // Tarik Semua Data dari Service
        $weatherData = (new WeatherService())->getCurrentWeather($lat, $lng);
        $economicData = (new WorldBankService())->getEconomicData($country->code);
        $newsData = (new NewsService())->getCountryNews($country->id, $country->name);

        // Masukkan ke Mesin Analisis Risiko (Yang kita buat di Tahap 4)
        $riskResult = (new RiskAnalysisService())->calculateCountryRisk($weatherData, $economicData, $newsData);

        return response()->json([
            'country' => $country,
            'risk_analysis' => $riskResult,
            'raw_data' => [
                'weather' => $weatherData,
                'economy' => $economicData,
            ]
        ]);
    }

    // 3. GET /api/currency -> Ambil nilai tukar mata uang global
    public function getCurrency()
    {
        $exchangeRate = new ExchangeRateService();
        $rates = $exchangeRate->getLatestRates('USD');
        
        return response()->json([
            'base' => 'USD', 
            'rates' => $rates
        ]);
    }
}