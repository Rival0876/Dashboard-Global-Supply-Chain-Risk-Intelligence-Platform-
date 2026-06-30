<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class TestWeatherController extends Controller
{
    public function cekCuaca()
    {
        // Memanggil service cuaca yang sudah kita buat
        $weatherService = new WeatherService();
        
        // Contoh koordinat: Kita pakai Lhokseumawe (Latitude: 5.1801, Longitude: 97.1507)
        // Anda juga bisa menggantinya dengan koordinat Jakarta (-6.2088, 106.8456)
        $hasil = $weatherService->getCurrentWeather(5.1801, 97.1507);
        
        // Menampilkan hasilnya dalam bentuk JSON di browser
        return response()->json([
            'pesan' => 'Testing API Cuaca Berhasil!',
            'lokasi' => 'Lhokseumawe',
            'data_cuaca' => $hasil
        ]);
    }
}