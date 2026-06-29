<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('risk_scores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained('countries')->onDelete('cascade'); // Negara yang dihitung 
        $table->date('date'); // Tanggal penghitungan 
        $table->integer('weather_score')->default(0); // Skor risiko cuaca 
        $table->integer('inflation_score')->default(0); // Skor risiko inflasi 
        $table->integer('currency_score')->default(0); // Skor risiko mata uang 
        $table->integer('news_score')->default(0); // Skor analisis sentimen 
        $table->integer('total_risk')->default(0); // Hasil akhir Risk Score 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
