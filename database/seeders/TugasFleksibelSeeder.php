<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class TugasFleksibelSeeder extends Seeder
{
    public function run()
    {
        // Ambil user pertama di database (User yang lagi lo pake login sekarang)
        $user = User::first();

        if (!$user) {
            $this->command->error('Belum ada user. Silakan login/register dulu di web biar user-nya kebuat!');
            return;
        }

        $tugas = [
            [
                'nama_kegiatan' => 'Mengerjakan Laporan AI Study Summarizer',
                'kategori'      => 'Tugas Kuliah',
                'tipe'          => 'fleksibel',
                'status'        => 'aktif',
                'deadline'      => 'Rabu',
                'durasi'        => 3,
                'is_scheduled'  => false,
                'hari'          => null,
                'jam_mulai'     => null,
                'jam_selesai'   => null,
            ],
            [
                'nama_kegiatan' => 'Revisi UI Figma ClassSync',
                'kategori'      => 'Project',
                'tipe'          => 'fleksibel',
                'status'        => 'aktif',
                'deadline'      => 'Jumat',
                'durasi'        => 2,
                'is_scheduled'  => false,
                'hari'          => null,
                'jam_mulai'     => null,
                'jam_selesai'   => null,
            ],
            [
                'nama_kegiatan' => 'Ngoding Dynamic Island macOS (Swift)',
                'kategori'      => 'Hobi',
                'tipe'          => 'fleksibel',
                'status'        => 'aktif',
                'deadline'      => 'Kamis',
                'durasi'        => 4,
                'is_scheduled'  => false,
                'hari'          => null,
                'jam_mulai'     => null,
                'jam_selesai'   => null,
            ],
            [
                'nama_kegiatan' => 'Riset Public API POS System',
                'kategori'      => 'Project',
                'tipe'          => 'fleksibel',
                'status'        => 'aktif',
                'deadline'      => 'Selasa',
                'durasi'        => 2,
                'is_scheduled'  => false,
                'hari'          => null,
                'jam_mulai'     => null,
                'jam_selesai'   => null,
            ],
            [
                'nama_kegiatan' => 'Setup VPS IDCloudHost & Docker',
                'kategori'      => 'DevOps',
                'tipe'          => 'fleksibel',
                'status'        => 'aktif',
                'deadline'      => 'Jumat',
                'durasi'        => 3,
                'is_scheduled'  => false,
                'hari'          => null,
                'jam_mulai'     => null,
                'jam_selesai'   => null,
            ],
        ];

        $count = 0;
        foreach ($tugas as $t) {
            // Biar nggak numpuk kalau lo jalanin seeder-nya berkali-kali
            $exists = Activity::where('user_id', $user->id)
                ->where('nama_kegiatan', $t['nama_kegiatan'])
                ->where('is_scheduled', false)
                ->exists();

            if (!$exists) {
                $t['user_id'] = $user->id;
                Activity::create($t);
                $count++;
            }
        }

        $this->command->info("Seeder sukses! {$count} tugas fleksibel berhasil disuntikkan ke akun: {$user->name}");
    }
}