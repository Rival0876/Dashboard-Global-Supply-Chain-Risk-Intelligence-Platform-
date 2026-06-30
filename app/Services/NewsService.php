<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\PositiveWord;
use App\Models\NegativeWord;
use App\Models\NewsCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NewsService
{
    /**
     * Mengambil berita dari GNews dan menganalisis sentimennya
     */
    public function getCountryNews($countryId, $countryName)
    {
        // 1. CEK CACHE: Mencegah API GNews kita cepat limit (habis kuota)
        // Kita cek apakah ada berita negara ini yang diambil dalam 24 jam terakhir
        $cachedNews = NewsCache::where('country_id', $countryId)
            ->where('fetched_at', '>=', Carbon::now()->subDay())
            ->get();

        // Jika ada di database, gunakan data itu (jangan panggil API lagi)
        if ($cachedNews->isNotEmpty()) {
            return $cachedNews;
        }

        // 2. PANGGIL API: Jika tidak ada di cache, kita tarik dari GNews
        $apiKey = env('GNEWS_API_KEY');
        // Query spesifik untuk logistik & ekonomi
        $query = "economy OR logistics OR supply chain " . $countryName; 

        try {
            $response = Http::get("https://gnews.io/api/v4/search", [
                'q' => $query,
                'lang' => 'en',
                'max' => 5, // Ambil 5 berita saja
                'apikey' => $apiKey
            ]);

            if ($response->successful()) {
                $articles = $response->json()['articles'] ?? [];
                $results = [];

                foreach ($articles as $article) {
                    $textToAnalyze = $article['title'] . " " . $article['description'];
                    
                    // 3. ANALISIS SENTIMEN
                    $sentiment = $this->analyzeSentiment($textToAnalyze);

                    // 4. SIMPAN KE CACHE (Database)
                    $news = NewsCache::create([
                        'country_id' => $countryId,
                        'title' => $article['title'],
                        'content' => $article['description'],
                        'sentiment_result' => $sentiment,
                        'fetched_at' => Carbon::now(),
                    ]);

                    $results[] = $news;
                }
                return $results;
            }
        } catch (\Exception $e) {
            Log::error('News API Error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Algoritma Sentiment Analysis (Lexicon Based) sesuai tugas dosen
     */
    private function analyzeSentiment($text)
    {
        // Ambil kamus kata dari database (menjadi array)
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // Bersihkan teks dari tanda baca dan ubah jadi huruf kecil
        $text = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $text));
        
        // Pecah kalimat menjadi per-kata
        $words = explode(' ', $text);

        $positiveScore = 0;
        $negativeScore = 0;

        // Hitung skor
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
            }
        }

        // Tentukan hasil akhir
        if ($positiveScore > $negativeScore) {
            return 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }
}