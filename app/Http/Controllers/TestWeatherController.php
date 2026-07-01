<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Services\NewsService;
use App\Models\Country;
use App\Services\ExchangeRateService;
use App\Services\WorldBankService;
use App\Services\RiskAnalysisService;
use App\Services\CountryService;

class TestWeatherController extends Controller
{
    public function cekCuaca()
    {
        $weatherService = new WeatherService();
        $hasil = $weatherService->getCurrentWeather(5.1801, 97.1507);
        
        return response()->json([
            'pesan' => 'Testing API Cuaca Berhasil!',
            'lokasi' => 'Lhokseumawe',
            'data_cuaca' => $hasil
        ]);
    }

    public function cekBerita()
    {
        // Buat data dummy negara untuk ditest
        $country = Country::firstOrCreate(
            ['name' => 'Germany'], 
            ['code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe']
        );

        $newsService = new NewsService();
        $hasilBerita = $newsService->getCountryNews($country->id, $country->name);

        return response()->json([
            'pesan' => 'Testing API Berita & Analisis Sentimen',
            'negara' => $country->name,
            'data_berita' => $hasilBerita
        ]);
    }

    public function cekEkonomi()
    {
        $country = Country::firstOrCreate(
            ['name' => 'Indonesia'], 
            ['code' => 'ID', 'currency_code' => 'IDR', 'region' => 'Asia']
        );

        $worldBank = new WorldBankService();
        $hasilEkonomi = $worldBank->getEconomicData($country->code);

        return response()->json([
            'pesan' => 'Testing API World Bank Berhasil!',
            'negara' => $country->name,
            'data_ekonomi' => $hasilEkonomi
        ]);
    }

    public function cekKurs()
    {
        $exchange = new ExchangeRateService();
        $kursRupiah = $exchange->convertUsdTo('IDR');
        $kursEuro = $exchange->convertUsdTo('EUR');

        return response()->json([
            'pesan' => 'Testing API Exchange Rate Berhasil!',
            '1_USD_to_IDR' => $kursRupiah,
            '1_USD_to_EUR' => $kursEuro
        ]);
    }

    public function cekRisikoTotal()
    {
        $country = Country::firstOrCreate(
            ['name' => 'Germany'], 
            ['code' => 'DE', 'currency_code' => 'EUR', 'region' => 'Europe']
        );

        // 1. Tarik Data Cuaca (Contoh koordinat Berlin, Jerman)
        $weatherService = new WeatherService();
        $weatherData = $weatherService->getCurrentWeather(52.5200, 13.4050);

        // 2. Tarik Data Ekonomi
        $worldBank = new WorldBankService();
        $economicData = $worldBank->getEconomicData($country->code);

        // 3. Tarik Data Berita
        $newsService = new NewsService();
        $newsData = $newsService->getCountryNews($country->id, $country->name);

        // 4. MASUKKAN KE DALAM OTAK ALGORITMA
        $riskAnalyzer = new RiskAnalysisService();
        $hasilRisiko = $riskAnalyzer->calculateCountryRisk($weatherData, $economicData, $newsData);

        return response()->json([
            'pesan' => 'Analisis Risiko Rantai Pasok Berhasil!',
            'negara' => $country->name,
            'keputusan_sistem' => $hasilRisiko,
            'data_mentah' => [
                'cuaca' => $weatherData,
                'ekonomi' => $economicData,
                'jumlah_berita_dianalisis' => count($newsData)
            ]
        ]);
    }

    public function cekNegara()
    {
        $countryService = new CountryService();
        
        // Kita coba cari data negara "Australia" dan otomatis simpan ke DB
        $hasilNegara = $countryService->getAndSaveCountryInfo('Australia');

        if ($hasilNegara) {
            return response()->json([
                'pesan' => 'Testing REST Countries API Berhasil!',
                'data_disimpan' => $hasilNegara
            ]);
        } else {
            return response()->json([
                'pesan' => 'Gagal! Negara tidak ditemukan atau API error.'
            ], 404);
        }
    }
}