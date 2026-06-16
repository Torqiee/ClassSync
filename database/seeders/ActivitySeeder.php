<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil user pertama di database sebagai pemilik jadwal
        $user = User::first();

        if (!$user) {
            $this->command->error('Tidak ada user! Pastikan kamu sudah mendaftar/login setidaknya 1 kali.');
            return;
        }

        // Hapus data lama (truncate) agar jadwal tidak dobel saat seeder dijalankan berulang kali
        Activity::truncate();

        $activities = [
            // ==========================================
            // JADWAL TETAP (HARD CONSTRAINT)
            // ==========================================
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Kuliah Konsep Kecerdasan Artifisial (KKA)',
                'kategori'      => 'Kuliah',
                'tipe'          => 'tetap',
                'hari'          => 'Senin',
                'jam_mulai'     => '08:00',
                'jam_selesai'   => '10:00',
                'is_scheduled'  => true,
            ],
            // 🚨 INI ADALAH JADWAL YANG MEMICU BENTROK 🚨
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Rapat BEM Dadakan (Wajib)',
                'kategori'      => 'Organisasi',
                'tipe'          => 'tetap',
                'hari'          => 'Senin',
                'jam_mulai'     => '09:00', // Nabrak jam Kuliah KKA!
                'jam_selesai'   => '11:00',
                'is_scheduled'  => true,
            ],
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Praktikum Struktur Data',
                'kategori'      => 'Kuliah',
                'tipe'          => 'tetap',
                'hari'          => 'Selasa',
                'jam_mulai'     => '10:00',
                'jam_selesai'   => '12:00',
                'is_scheduled'  => true,
            ],
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Rapat Divisi Frontend UKM Robotika',
                'kategori'      => 'UKM',
                'tipe'          => 'tetap',
                'hari'          => 'Rabu',
                'jam_mulai'     => '18:00',
                'jam_selesai'   => '20:00',
                'is_scheduled'  => true,
            ],
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Dinner & Quality Time',
                'kategori'      => 'Lainnya',
                'tipe'          => 'tetap',
                'hari'          => 'Jumat',
                'jam_mulai'     => '19:00',
                'jam_selesai'   => '21:00',
                'is_scheduled'  => true,
            ],

            // ==========================================
            // TUGAS FLEKSIBEL (UNTUK AI)
            // ==========================================
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Ngerjain UI/UX Figma ClassSync',
                'kategori'      => 'Tugas',
                'tipe'          => 'fleksibel',
                'deadline'      => 'Rabu',
                'durasi'        => 2,
                'is_scheduled'  => false,
            ],
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Bikin Paper NLP sambil dengerin Taylor Swift',
                'kategori'      => 'Tugas',
                'tipe'          => 'fleksibel',
                'deadline'      => 'Kamis',
                'durasi'        => 3,
                'is_scheduled'  => false,
            ],
            [
                'user_id'       => $user->id,
                'nama_kegiatan' => 'Latihan Soal LeetCode',
                'kategori'      => 'Lainnya',
                'tipe'          => 'fleksibel',
                'deadline'      => 'Jumat',
                'durasi'        => 2,
                'is_scheduled'  => false,
            ],
        ];

        // Looping untuk menyimpan semua data array di atas ke MongoDB
        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        $this->command->info('Seeder berhasil! Jadwal berantakan (bentrok) telah berhasil di-generate.');
    }
}