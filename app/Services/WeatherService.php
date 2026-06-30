<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Mengambil data cuaca saat ini dari Open-Meteo API
     */
    public function getCurrentWeather($latitude, $longitude)
    {
        try {
            // Memanggil API Open-Meteo (Gratis, tanpa API Key)
            $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,rain,wind_speed_10m,weather_code',
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Mengambil kode cuaca WMO (World Meteorological Organization)
                $weatherCode = $data['current']['weather_code'] ?? 0;
                
                // Menghitung status risiko badai berdasarkan spesifikasi tugas
                $stormRisk = $this->calculateStormRisk($weatherCode);

                return [
                    'temperature' => $data['current']['temperature_2m'] ?? 0, // Temperatur
                    'rain' => $data['current']['rain'] ?? 0,                 // Curah hujan
                    'wind_speed' => $data['current']['wind_speed_10m'] ?? 0, // Kecepatan angin
                    'weather_code' => $weatherCode,
                    'storm_risk' => $stormRisk,                              // Risiko badai
                ];
            }

            return null; // Jika API gagal merespons dengan benar
            
        } catch (\Exception $e) {
            // Mencatat error di file log Laravel (storage/logs/laravel.log) jika server API mati
            Log::error('Weather API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Algoritma sederhana penentu Risiko Badai berdasarkan WMO Weather Code
     */
    private function calculateStormRisk($code)
    {
        // Kode 95, 96, 99 adalah Thunderstorm (Badai Petir)
        if (in_array($code, [95, 96, 99])) {
            return 'High'; // Risiko Tinggi
        } 
        // Kode 61-65 (Hujan), 80-82 (Hujan Deras)
        elseif (in_array($code, [61, 63, 65, 80, 81, 82])) {
            return 'Medium'; // Risiko Sedang
        }
        
        // Selain itu (Cerah, Berawan, dsb)
        return 'Low'; // Risiko Rendah
    }
}