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
    Schema::create('ports', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama pelabuhan 
        $table->foreignId('country_id')->constrained('countries')->onDelete('cascade'); // Relasi ke negara 
        $table->decimal('latitude', 10, 8); // Titik koordinat 
        $table->decimal('longitude', 11, 8); // Titik koordinat 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
