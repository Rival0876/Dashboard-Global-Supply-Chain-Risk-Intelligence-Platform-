<?php

namespace App\Services;

class RiskAnalysisService
{
    /**
     * Kalkulasi Total Skor Risiko berdasarkan berbagai faktor
     */
    public function calculateCountryRisk($weatherData, $economicData, $newsData)
    {
        // 1. Sentimen Berita (Bobot 40% sesuai spesifikasi tugas)
        $sentimentRisk = $this->calculateSentimentRisk($newsData);

        // 2. Cuaca (Bobot 30%)
        $weatherRisk = $this->calculateWeatherRisk($weatherData);

        // 3. Inflasi (Bobot 20%)
        $inflationRisk = $this->calculateInflationRisk($economicData['inflation'] ?? 0);

        // 4. Nilai Tukar / Mata Uang (Bobot 10%)
        // Untuk tahap ini kita set statis 50 (Netral) dulu, nantinya bisa dikembangkan
        $currencyRisk = 50; 

        // TOTAL SKOR = Penggabungan berdasarkan persentase bobot
        $totalRiskScore = ($sentimentRisk * 0.4) + ($weatherRisk * 0.3) + ($inflationRisk * 0.2) + ($currencyRisk * 0.1);

        // Menentukan Status Risiko
        $status = 'Low Risk';
        if ($totalRiskScore >= 65) {
            $status = 'High Risk';
        } elseif ($totalRiskScore >= 40) {
            $status = 'Medium Risk';
        }

        return [
            'total_score' => round($totalRiskScore, 2),
            'status' => $status,
            'breakdown' => [
                'news_sentiment_risk' => $sentimentRisk,
                'weather_risk' => $weatherRisk,
                'inflation_risk' => $inflationRisk,
                'currency_risk' => $currencyRisk,
            ]
        ];
    }

    /**
     * Mesin Analisis Sentimen Dasar (Lexicon Based)
     */
    private function calculateSentimentRisk($newsData)
    {
        // Catatan: Sesuai tugas, nantinya kata-kata ini ditarik dari database (tabel positive_words & negative_words).
        // Untuk testing algoritma awal, kita pakai array (Kamus) langsung di sini:
        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'recovery', 'boom'];
        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'drop', 'risk', 'tension'];

        $posCount = 0;
        $negCount = 0;

        // Jika tidak ada berita, anggap netral
        if (empty($newsData) || count($newsData) == 0) return 50;

        foreach ($newsData as $news) {
            // Gabungkan judul dan deskripsi berita, ubah ke huruf kecil
            $text = strtolower($news['title'] . ' ' . $news['description']);
            
            foreach ($positiveWords as $word) {
                if (strpos($text, $word) !== false) $posCount++;
            }
            foreach ($negativeWords as $word) {
                if (strpos($text, $word) !== false) $negCount++;
            }
        }

        // Semakin banyak sentimen negatif = Semakin TINGGI risiko (Skor mendekati 100)
        if ($negCount > $posCount) return 85; // High Risk
        if ($posCount > $negCount) return 15; // Low Risk
        return 50; // Neutral Risk (Berimbang)
    }

    /**
     * Algoritma Risiko Cuaca Ekstrem (Open-Meteo)
     */
    private function calculateWeatherRisk($weatherData)
    {
        if (!$weatherData) return 50;
        
        $windspeed = $weatherData['windspeed'] ?? 0;
        
        // Asumsi: Angin di atas 40km/jam sangat berisiko untuk logistik kapal/pesawat
        if ($windspeed > 40) return 90; // High Risk
        if ($windspeed > 20) return 50; // Medium Risk
        return 10; // Low Risk
    }

    /**
     * Algoritma Risiko Inflasi (World Bank)
     */
    private function calculateInflationRisk($inflation)
    {
        // Asumsi: Inflasi di atas 8% sangat mengganggu biaya produksi dan logistik
        if ($inflation > 8) return 90; 
        if ($inflation > 4) return 60;
        return 20;
    }
}