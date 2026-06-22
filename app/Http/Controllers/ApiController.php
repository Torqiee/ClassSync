<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AiSchedulerService;
use App\Models\Activity;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan passwordnya cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau sandi salah!'
            ], 401);
        }

        // Buat token akses
        $token = $user->createToken('flutter_mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    // ==========================================
    // FUNGSI LOGIN VIA GOOGLE
    // ==========================================
    public function googleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
        ]);

        // Cek apakah email ini sudah terdaftar di database
        $user = User::where('email', $request->email)->first();

        // Jika pengguna baru pertama kali login pakai Google, buatkan akun otomatis
        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make(uniqid()), // Password acak, karena dia pakai Google
                'google_id' => $request->google_id,
            ]);
        } else {
            // Opsional: Update google_id jika dia sebelumnya daftar manual lalu iseng login via Google
            if (!$user->google_id) {
                $user->update(['google_id' => $request->google_id]);
            }
        }

        // Buat token akses Sanctum
        $token = $user->createToken('flutter_mobile_app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    // ==========================================
    // FUNGSI UTK MENGIRIM DATA ASLI DARI MONGO
    // ==========================================
    public function getDashboardData(Request $request)
    {
        $user = $request->user();
        
        // 1. Ambil semua aktivitas yang aktif
        $allActivities = \App\Models\Activity::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'aktif')->orWhereNull('status');
            })
            ->get();

        $jadwal = [
            'Senin' => [], 'Selasa' => [], 'Rabu' => [], 'Kamis' => [], 'Jumat' => [], 'Sabtu' => [], 'Minggu' => []
        ];

        // 2. Data Kalender (Hanya jadwal yang sudah punya hari)
        $scheduledActivities = $allActivities->where('is_scheduled', true)->whereNotNull('hari');

        foreach ($scheduledActivities as $aktivitas) {
            $hari = $aktivitas->hari;
            if ($hari && array_key_exists($hari, $jadwal)) {
                $jadwal[$hari][] = [
                    'id' => $aktivitas->id,
                    'nama' => $aktivitas->nama_kegiatan,
                    'kategori' => $aktivitas->kategori ?? 'Kuliah',
                    'jam_mulai' => $aktivitas->jam_mulai,
                    'jam_selesai' => $aktivitas->jam_selesai,
                    'ruangan' => $aktivitas->ruangan ?? $aktivitas->kategori ?? 'Lainnya',
                    'sifat_jadwal' => ucfirst($aktivitas->tipe ?? 'tetap'), 
                    'batas_akhir' => $aktivitas->batas_akhir,
                ];
            }
        }

        // Urutkan jadwal per hari berdasarkan jam_mulai agar rapi saat dicek
        foreach ($jadwal as $hari => $acts) {
            usort($jadwal[$hari], function($a, $b) {
                return strtotime($a['jam_mulai']) - strtotime($b['jam_mulai']);
            });
        }

        // 3. --- MENGHITUNG STATISTIK ---
        $totalAktivitas = $allActivities->count();

        // Cek Bentrok
        $bentrokCount = 0;
        foreach ($jadwal as $hari => $acts) {
            for ($i = 0; $i < count($acts) - 1; $i++) {
                $currentEnd = strtotime($acts[$i]['jam_selesai']);
                $nextStart = strtotime($acts[$i+1]['jam_mulai']);
                if ($currentEnd > $nextStart) {
                    $bentrokCount++;
                }
            }
        }

        $unscheduledCount = $allActivities->where('tipe', 'fleksibel')->where('is_scheduled', false)->count();

        // 4. Penentuan Status AI
        if ($bentrokCount > 0) {
            $efisiensiAI = "Bentrok!";
            $warnaAI = "red";
        } elseif ($unscheduledCount > 0) {
            $efisiensiAI = "Perlu Generate";
            $warnaAI = "orange";
        } elseif ($totalAktivitas == 0) {
            $efisiensiAI = "Kosong";
            $warnaAI = "grey";
        } else {
            $efisiensiAI = "Optimal";
            $warnaAI = "blue";
        }

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'jadwal' => $jadwal,
                'total_aktivitas' => $totalAktivitas,
                'efisiensi_ai' => $efisiensiAI,
                'warna_ai' => $warnaAI
            ]
        ], 200);
    }

    // ==========================================
    // FUNGSI UNTUK MENAMBAH JADWAL BARU (VERSI AI)
    // ==========================================
    public function tambahAktivitas(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'kategori' => 'required|string',
            'sifat_jadwal' => 'required|string', 
        ]);

        $user = $request->user();
        $isTetap = $request->sifat_jadwal === 'Tetap';

        $aktivitasBaru = \App\Models\Activity::create([
            'user_id' => $user->id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'kategori' => $request->kategori,
            
            // KUNCI 2: Otomatis tambahkan field yang dibutuhkan oleh versi Web!
            'tipe' => $isTetap ? 'tetap' : 'fleksibel',
            'status' => 'aktif',
            // Jika jadwal tetap, otomatis ter-schedule. Jika fleksibel, tunggu AI (false)
            'is_scheduled' => $isTetap ? true : false, 
            
            // Data untuk Jadwal Tetap
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            
            // Data untuk Tugas Fleksibel
            'batas_akhir' => $request->batas_akhir,
            'durasi' => $request->durasi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil ditambahkan!',
            'data' => $aktivitasBaru
        ], 201);
    }

    // ==========================================
    // FUNGSI UNTUK MENGHAPUS JADWAL
    // ==========================================
    public function hapusAktivitas(Request $request, $id)
    {
        $user = $request->user();

        // Cari aktivitas berdasarkan ID dan pastikan itu milik user yang sedang login
        $aktivitas = \App\Models\Activity::where('_id', $id)->where('user_id', $user->id)->first();

        if (!$aktivitas) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }

        $aktivitas->delete(); // Hapus dari MongoDB

        return response()->json(['success' => true, 'message' => 'Aktivitas berhasil dihapus'], 200);
    }

    // ==========================================
    // FUNGSI UNTUK MENGUBAH / UPDATE JADWAL
    // ==========================================
    public function ubahAktivitas(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'kategori' => 'required|string',
            'sifat_jadwal' => 'required|string', 
        ]);

        $user = $request->user();

        // Cari data di MongoDB berdasarkan ID dan kepemilikan user
        $aktivitas = \App\Models\Activity::where('_id', $id)->where('user_id', $user->id)->first();

        if (!$aktivitas) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }

        $isTetap = $request->sifat_jadwal === 'Tetap';

        // Update data dengan struktur NoSQL yang bersih
        $aktivitas->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'kategori' => $request->kategori,
            'tipe' => $isTetap ? 'tetap' : 'fleksibel',

            // Jika beralih ke Jadwal Tetap, isi datanya dan hapus data fleksibel
            'hari' => $isTetap ? $request->hari : null,
            'jam_mulai' => $isTetap ? $request->jam_mulai : null,
            'jam_selesai' => $isTetap ? $request->jam_selesai : null,

            // Jika beralih ke Tugas Fleksibel, isi datanya dan hapus data tetap
            'batas_akhir' => !$isTetap ? $request->batas_akhir : null,
            'durasi' => !$isTetap ? $request->durasi : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivitas berhasil diperbarui!',
            'data' => $aktivitas
        ], 200);
    }

    // ==========================================
    // FUNGSI UNTUK GENERATE JADWAL VIA AI (REAL LOGIC)
    // ==========================================
    public function generateJadwal(Request $request, AiSchedulerService $aiService)
    {
        try {
            // Langsung panggil otak AI yang sama persis kayak versi Web
            $result = $aiService->generateSchedule();

            // Biasanya dari web balikan statusnya 'success', 'info', atau 'error'
            $isSuccess = ($result['status'] === 'success');

            return response()->json([
                'success' => $isSuccess,
                'message' => $result['message']
            ], $isSuccess ? 200 : 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Waduh, AI gagal menyusun jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // FUNGSI RIWAYAT & ARSIP (API)
    // ==========================================

    public function getHistoryApi(Request $request)
    {
        $historyActivities = \App\Models\Activity::where('user_id', $request->user()->id)
                            ->where('status', 'selesai')
                            ->orderBy('diselesaikan_pada', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat berhasil diambil',
            'data' => $historyActivities
        ], 200);
    }

    public function archiveWeekApi(Request $request)
    {
        // Memindahkan HANYA tugas fleksibel yang aktif ke Riwayat.
        \App\Models\Activity::where('user_id', $request->user()->id)
                ->where('tipe', 'fleksibel')
                ->where(function($query) {
                    $query->where('status', 'aktif')
                          ->orWhereNull('status');
                })
                ->update([
                    'status' => 'selesai',
                    'diselesaikan_pada' => now()
                ]);

        return response()->json([
            'success' => true, 
            'message' => 'Kalender di-refresh! Semua tugas fleksibel dipindahkan ke Riwayat.'
        ], 200);
    }

    // ==========================================
    // FUNGSI UPDATE PROFIL & PASSWORD (API)
    // ==========================================

    public function updateProfileApi(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        // Hanya update email jika bukan akun Google
        if (!$user->google_id) {
            $user->email = $request->email;
        }
        $user->save();

        return response()->json([
            'success' => true, 
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user
        ], 200);
    }

    public function updatePasswordApi(Request $request)
    {
        $user = $request->user();

        // Tolak jika ini akun Google
        if ($user->google_id) {
            return response()->json(['success' => false, 'message' => 'Akun Google tidak menggunakan kata sandi.'], 400);
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cek apakah password lama cocok
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kata sandi saat ini salah!'], 400);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Kata sandi berhasil diperbarui!'], 200);
    }
}