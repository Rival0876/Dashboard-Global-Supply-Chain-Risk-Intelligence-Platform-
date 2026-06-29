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
    Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: Germany, Indonesia [cite: 94]
        $table->string('code', 5)->nullable(); // Kode negara [cite: 94]
        $table->string('currency_code', 10)->nullable(); // Mata uang [cite: 94]
        $table->string('region')->nullable(); // Wilayah [cite: 94]
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
