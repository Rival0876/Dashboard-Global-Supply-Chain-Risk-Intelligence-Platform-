<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Services\NewsService;
use App\Models\Country;

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
}