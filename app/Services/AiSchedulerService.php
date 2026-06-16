<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class AiSchedulerService
{
    protected $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    // Jam operasional produktif (jam mulai)
    protected $jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

    public function generateSchedule()
    {
        $userId = Auth::id();
        
        // 1. Ambil Pengaturan User
        $settings = \App\Models\Setting::firstOrCreate(
            ['user_id' => $userId],
            ['istirahat_1_jam' => true, 'maks_3_kegiatan' => false, 'waktu_produktif' => true]
        );

        // 2. Sesuaikan Domain Jam berdasarkan preferensi waktu produktif
        if ($settings->waktu_produktif) {
            $this->jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        } else {
            // Jika dimatikan, AI boleh mengatur jadwal sampai jam 9 malam
            $this->jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];
        }
        
        $jadwal_tetap = Activity::where('user_id', $userId)->where('tipe', 'tetap')->get();
        $tugas_fleksibel = Activity::where('user_id', $userId)->where('tipe', 'fleksibel')->where('is_scheduled', false)->get()->values();

        if ($tugas_fleksibel->isEmpty()) {
            return ['status' => 'info', 'message' => 'Tidak ada tugas baru yang perlu dijadwalkan.'];
        }

        $kalender = [];
        foreach ($this->hari_list as $hari) {
            foreach ($this->jam_produktif as $jam) {
                $kalender[$hari][$jam] = null;
            }
        }

        foreach ($jadwal_tetap as $kegiatan) {
            $start_int = (int) substr($kegiatan->jam_mulai, 0, 2);
            $end_int = (int) substr($kegiatan->jam_selesai, 0, 2);
            $durasi = $end_int - $start_int;
            
            for ($i = 0; $i < $durasi; $i++) {
                $jam_format = sprintf("%02d:00", $start_int + $i);
                if (array_key_exists($jam_format, $kalender[$kegiatan->hari])) {
                    $kalender[$kegiatan->hari][$jam_format] = $kegiatan->id;
                }
            }
        }

        // 3. Fungsi Constraint dengan Aturan Dinamis
        $isValid = function($hari, $jam_mulai, $durasi_tugas, $deadline) use (&$kalender, $settings) {
            
            // --- ATURAN DINAMIS 1: Maksimal 3 Kegiatan Per Hari ---
            if ($settings->maks_3_kegiatan) {
                // Hitung ID unik (kegiatan) yang ada di hari tersebut
                $kegiatan_hari_ini = count(array_unique(array_filter($kalender[$hari])));
                // +1 karena kita mau nambah kegiatan baru ini
                if ($kegiatan_hari_ini + 1 > 3) return false; 
            }

            // Aturan Dasar (Hard Constraint)
            $hari_idx = array_search($hari, $this->hari_list);
            $dl_idx = array_search($deadline, $this->hari_list);
            if ($hari_idx > $dl_idx) return false;

            $start_int = (int) substr($jam_mulai, 0, 2);

            for ($i = 0; $i < $durasi_tugas; $i++) {
                $jam_cek = sprintf("%02d:00", $start_int + $i);
                if (!in_array($jam_cek, $this->jam_produktif) || $kalender[$hari][$jam_cek] !== null) {
                    return false;
                }
            }

            // --- ATURAN DINAMIS 2: Istirahat Minimal 1 Jam ---
            if ($settings->istirahat_1_jam) {
                $jam_sebelum = sprintf("%02d:00", $start_int - 1);
                if (in_array($jam_sebelum, $this->jam_produktif) && $kalender[$hari][$jam_sebelum] !== null) {
                    return false;
                }

                $jam_setelah = sprintf("%02d:00", $start_int + $durasi_tugas);
                if (in_array($jam_setelah, $this->jam_produktif) && $kalender[$hari][$jam_setelah] !== null) {
                    return false;
                }
            }

            return true;
        };

        // --- SISA KODE ALGORITMA BACKTRACK SAMA SEPERTI SEBELUMNYA ---
        $backtrack = function($index) use (&$tugas_fleksibel, &$kalender, &$isValid, &$backtrack) {
            if ($index === count($tugas_fleksibel)) return true;
            $tugas = $tugas_fleksibel[$index];
            $durasi_tugas = $tugas->durasi;

            foreach ($this->hari_list as $hari) {
                foreach ($this->jam_produktif as $jam) {
                    if ($isValid($hari, $jam, $durasi_tugas, $tugas->deadline)) {
                        $start_int = (int) substr($jam, 0, 2);
                        for ($i = 0; $i < $durasi_tugas; $i++) {
                            $jam_assign = sprintf("%02d:00", $start_int + $i);
                            $kalender[$hari][$jam_assign] = $tugas->id;
                        }
                        if ($backtrack($index + 1)) return true;
                        for ($i = 0; $i < $durasi_tugas; $i++) {
                            $jam_unassign = sprintf("%02d:00", $start_int + $i);
                            $kalender[$hari][$jam_unassign] = null;
                        }
                    }
                }
            }
            return false;
        };

        $success = $backtrack(0);

        if ($success) {
            foreach ($tugas_fleksibel as $tugas) {
                $jam_mulai_tugas = null;
                $hari_tugas = null;
                foreach ($this->hari_list as $hari) {
                    foreach ($this->jam_produktif as $jam) {
                        if ($kalender[$hari][$jam] === $tugas->id && $jam_mulai_tugas === null) {
                            $hari_tugas = $hari;
                            $jam_mulai_tugas = $jam;
                            break 2;
                        }
                    }
                }
                if ($jam_mulai_tugas !== null) {
                    $tugas->hari = $hari_tugas;
                    $tugas->jam_mulai = $jam_mulai_tugas;
                    $jam_int = (int) substr($jam_mulai_tugas, 0, 2);
                    $tugas->jam_selesai = sprintf("%02d:00", $jam_int + $tugas->durasi);
                    $tugas->is_scheduled = true;
                    $tugas->save();
                }
            }
            return ['status' => 'success', 'message' => 'AI berhasil menemukan jadwal optimal sesuai aturan preferensi!'];
        }
        return ['status' => 'error', 'message' => 'AI gagal menemukan waktu luang yang mematuhi preferensi saat ini.'];
    }
}