<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestWeatherController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-cuaca', [TestWeatherController::class, 'cekCuaca']);
Route::get('/test-berita', [TestWeatherController::class, 'cekBerita']);
Route::get('/test-ekonomi', [TestWeatherController::class, 'cekEkonomi']);
Route::get('/test-kurs', [TestWeatherController::class, 'cekKurs']);
Route::get('/test-risiko', [TestWeatherController::class, 'cekRisikoTotal']);
Route::get('/test-negara', [TestWeatherController::class, 'cekNegara']);