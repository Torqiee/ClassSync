<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SettingController;

// Rute Publik
Route::post('/login', [ApiController::class, 'login']);
Route::post('/google-login', [ApiController::class, 'googleLogin']);

// Rute Proteksi (Wajib bawa Token untuk masuk ke sini)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::get('/dashboard', [ApiController::class, 'getDashboardData']); 
    
    // Tambahkan baris ini untuk menerima data baru:
    Route::post('/dashboard/tambah', [ApiController::class, 'tambahAktivitas']); 
    Route::delete('/dashboard/hapus/{id}', [ApiController::class, 'hapusAktivitas']);
    Route::put('/dashboard/ubah/{id}', [ApiController::class, 'ubahAktivitas']);
    Route::post('/dashboard/generate', [ApiController::class, 'generateJadwal']);


    Route::get('/settings', [SettingController::class, 'getApi']);
    Route::post('/settings', [SettingController::class, 'updateApi']);

    Route::get('/history', [ApiController::class, 'getHistoryApi']);
    Route::post('/archive-week', [ApiController::class, 'archiveWeekApi']);

    Route::put('/profile', [ApiController::class, 'updateProfileApi']);
    Route::put('/password', [ApiController::class, 'updatePasswordApi']);
});