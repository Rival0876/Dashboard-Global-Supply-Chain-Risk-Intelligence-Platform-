<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\TestWeatherController;

Route::get('/test-cuaca', [TestWeatherController::class, 'cekCuaca']);