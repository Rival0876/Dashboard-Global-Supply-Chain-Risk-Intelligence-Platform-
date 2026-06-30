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
    Schema::create('news_caches', function (Blueprint $table) {
        $table->id();
        
        // DUA KOLOM INI YANG BIKIN ERROR KARENA BELUM ADA 👇
        $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
        $table->timestamp('fetched_at'); 
        
        // Kolom standar untuk simpan berita (sesuaikan jika kamu punya nama kolom beda)
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        $table->string('url')->nullable();
        $table->string('sentiment')->nullable(); // Karena ini project Supply Chain Risk
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_caches');
    }
};
