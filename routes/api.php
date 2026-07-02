<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Endpoint sesuai permintaan tugas dosen kamu
Route::get('/countries', [ApiController::class, 'getCountries']);
Route::get('/risk', [ApiController::class, 'getRisk']);
Route::get('/currency', [ApiController::class, 'getCurrency']);

// Note: /api/ports dan /api/news akan kita tambahkan belakangan