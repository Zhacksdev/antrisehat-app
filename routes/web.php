<?php

use App\Http\Controllers\AntreanController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\JadwalDokterController;
use App\Http\Controllers\JanjiTemuController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
| Health check (PRD 2.7). Tanpa middleware session/cookie agar tetap bisa
| mengembalikan 503 ketika database (atau session store) tidak terjangkau.
*/
Route::get('/health', HealthController::class)
    ->withoutMiddleware([
        StartSession::class,
        ShareErrorsFromSession::class,
        ValidateCsrfToken::class,
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
    ])
    ->name('health');

Route::get('/', [AntreanController::class, 'index'])->name('antrean.index');

// Antrean
Route::post('/antrean', [AntreanController::class, 'store'])->name('antrean.store');
Route::post('/antrean/panggil', [AntreanController::class, 'panggil'])->name('antrean.panggil');
Route::get('/antrean/berjalan', [AntreanController::class, 'berjalan'])->name('antrean.berjalan');

// Janji temu
Route::get('/janji-temu', [JanjiTemuController::class, 'index'])->name('janji-temu.index');
Route::post('/janji-temu', [JanjiTemuController::class, 'store'])->name('janji-temu.store');

// CRUD dokter & jadwal praktik
Route::resource('dokters', DokterController::class);
Route::resource('jadwal-dokters', JadwalDokterController::class)
    ->parameters(['jadwal-dokters' => 'jadwalDokter']);
