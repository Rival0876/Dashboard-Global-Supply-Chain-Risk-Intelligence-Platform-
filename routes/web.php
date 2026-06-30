<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestWeatherController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-cuaca', [TestWeatherController::class, 'cekCuaca']);
Route::get('/test-berita', [TestWeatherController::class, 'cekBerita']);