<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $userId = Auth::id();

        // 1. Ambil semua aktivitas user (HANYA YANG AKTIF)
        // Filter inilah yang membuat tugas 'selesai' menghilang dari Dashboard
        $allActivities = \App\Models\Activity::where('user_id', $userId)
            ->where(function($query) {
                $query->where('status', 'aktif')
                      ->orWhereNull('status');
            })
            ->get();
        
        // Data kalender (Hanya jadwal yang sudah memiliki jam & hari)
        $scheduledActivities = $allActivities->where('is_scheduled', true)->whereNotNull('hari');
        $activities = $scheduledActivities->sortBy('jam_mulai')->groupBy('hari');

        // --- MENGHITUNG STATISTIK ---

        // A. Total Aktivitas
        $totalAktivitas = $allActivities->count();

        // B. Total Jam Terpakai
        $jamTerpakai = 0;
        foreach ($scheduledActivities as $act) {
            if ($act->jam_mulai && $act->jam_selesai) {
                $start = strtotime($act->jam_mulai);
                $end = strtotime($act->jam_selesai);
                if ($end > $start) {
                    $jamTerpakai += ($end - $start) / 3600; // Selisih diubah menjadi format jam
                }
            }
        }

        // C. Pengecekan Bentrok (Clash Detection)
        $bentrokCount = 0;
        foreach ($activities as $hari => $acts) {
            $actsArray = $acts->values(); // Reset index
            for ($i = 0; $i < count($actsArray) - 1; $i++) {
                $currentEnd = strtotime($actsArray[$i]->jam_selesai);
                $nextStart = strtotime($actsArray[$i+1]->jam_mulai);
                
                // Jika jadwal saat ini selesai LEBIH LAMA dari jadwal berikutnya mulai = BENTROK
                if ($currentEnd > $nextStart) {
                    $bentrokCount++;
                }
            }
        }

        // D. Efisiensi AI & Status Visual
        $unscheduledCount = $allActivities->where('tipe', 'fleksibel')->where('is_scheduled', false)->count();
        
        if ($bentrokCount > 0) {
            $efisiensiAI = "Bentrok!";
            $warnaAI = "text-red-600";
            $statusBentrok = $bentrokCount . " Kasus";
            $warnaBentrok = "text-red-600";
        } elseif ($unscheduledCount > 0) {
            $efisiensiAI = "Perlu Generate";
            $warnaAI = "text-amber-500";
            $statusBentrok = "0%";
            $warnaBentrok = "text-emerald-600";
        } elseif ($totalAktivitas == 0) {
            $efisiensiAI = "Kosong";
            $warnaAI = "text-gray-400";
            $statusBentrok = "-";
            $warnaBentrok = "text-gray-400";
        } else {
            $efisiensiAI = "Optimal";
            $warnaAI = "text-blue-600";
            $statusBentrok = "0%";
            $warnaBentrok = "text-emerald-600";
        }

        return view('dashboard', compact(
            'activities', 
            'totalAktivitas', 
            'jamTerpakai', 
            'statusBentrok', 
            'warnaBentrok', 
            'efisiensiAI', 
            'warnaAI'
        ));
    })->name('dashboard');

    Route::post('/activities/generate-ai', [ActivityController::class, 'generateAI'])->name('activities.generate');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');

    // History & Archive
    Route::get('/history', [ActivityController::class, 'history'])->name('activities.history');
    Route::post('/archive-week', [ActivityController::class, 'archiveWeek'])->name('activities.archive');

    // Rute Edit & Update & Delete
    Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Route untuk Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::get('/activities/reset', function () {
    // Kembalikan semua tugas fleksibel user ini ke status belum dijadwalkan
    // PASTIKAN HANYA MERESET YANG AKTIF (Tugas di History jangan diganggu)
    \App\Models\Activity::where('user_id', Auth::id())
        ->where('tipe', 'fleksibel')
        ->where(function($query) {
            $query->where('status', 'aktif')
                  ->orWhereNull('status');
        })
        ->update([
            'is_scheduled' => false,
            'hari' => null,
            'jam_mulai' => null,
            'jam_selesai' => null
        ]);
    return redirect()->route('dashboard')->with('info', 'Jadwal AI berhasil di-reset. Siap generate ulang!');
})->name('activities.reset');

// Route untuk Google Socialite
Route::get('auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';