<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BacktrackScenarioSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID User pertama otomatis (Levina)
        $user = \App\Models\User::first();
        if (!$user) {
            echo "User tidak ditemukan. Pastikan sudah ada akun terdaftar.\n";
            return;
        }
        $userId = $user->id;

        // Bersihkan jadwal lama
        DB::table('activities')->where('user_id', $userId)->delete();

        $activities = [
            // --- JADWAL TETAP (BLOKIR WAKTU) ---
            [
                'user_id' => $userId,
                'nama_kegiatan' => 'Kuliah KKA (Blokir Senin)',
                'kategori' => 'Kuliah',
                'tipe' => 'tetap',
                'status' => 'aktif',
                'hari' => 'Senin',
                'jam_mulai' => '12:00',
                'jam_selesai' => '20:00',
                'durasi' => 8,
                'deadline' => null,
                'is_scheduled' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'nama_kegiatan' => 'Praktikum (Blokir Selasa)',
                'kategori' => 'Kuliah',
                'tipe' => 'tetap',
                'status' => 'aktif',
                'hari' => 'Selasa',
                'jam_mulai' => '10:00',
                'jam_selesai' => '20:00',
                'durasi' => 10,
                'deadline' => null,
                'is_scheduled' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // --- TUGAS FLEKSIBEL (PEMICU BACKTRACK) ---
            [
                'user_id' => $userId,
                'nama_kegiatan' => 'Tugas A (Jebakan 2 Jam)',
                'kategori' => 'Tugas',
                'tipe' => 'fleksibel',
                'status' => 'aktif',
                'hari' => null,
                'jam_mulai' => null,
                'jam_selesai' => null,
                'durasi' => 2,
                'deadline' => 'Selasa',
                'is_scheduled' => false, // Set false biar AI tahu ini harus dijadwalkan
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'nama_kegiatan' => 'Tugas B (Korban 3 Jam)',
                'kategori' => 'Tugas',
                'tipe' => 'fleksibel',
                'status' => 'aktif',
                'hari' => null,
                'jam_mulai' => null,
                'jam_selesai' => null,
                'durasi' => 3,
                'deadline' => 'Selasa',
                'is_scheduled' => false, // Set false biar AI tahu ini harus dijadwalkan
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('activities')->insert($activities);
    }
}
