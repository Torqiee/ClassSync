<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class AiSchedulerService
{
    protected $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    // Jam operasional produktif (jam mulai)
    protected $jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

    // Batas jumlah node "check" yang direkam, agar payload trace tidak meledak
    // pada kasus dengan banyak tugas (algoritma tetap berjalan penuh, hanya logging dibatasi)
    protected $maxCheckSteps = 600;
    protected $checkStepCount = 0;

    public function generateSchedule()
    {
        $userId = Auth::id();
        $trace = [];

        $trace[] = [
            'type'  => 'start',
            'title' => 'Mulai: Generate Jadwal Mingguan',
        ];

        // 1. Ambil Pengaturan User
        $settings = \App\Models\Setting::firstOrCreate(
            ['user_id' => $userId],
            ['istirahat_1_jam' => true, 'maks_3_kegiatan' => false, 'waktu_produktif' => true]
        );

        // 2. Sesuaikan Domain Jam berdasarkan preferensi waktu produktif
        if ($settings->waktu_produktif) {
            $this->jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        } else {
            $this->jam_produktif = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];
        }

        $trace[] = [
            'type'   => 'process',
            'title'  => 'Memuat Preferensi AI',
            'detail' => sprintf(
                'Mode waktu produktif: %s · Istirahat 1 jam: %s · Maks 3 kegiatan/hari: %s · Domain jam aktif: %s',
                $settings->waktu_produktif ? 'Aktif (08:00–17:00)' : 'Nonaktif (08:00–21:00)',
                $settings->istirahat_1_jam ? 'Ya' : 'Tidak',
                $settings->maks_3_kegiatan ? 'Ya' : 'Tidak',
                implode(', ', $this->jam_produktif)
            ),
        ];

        $jadwal_tetap = Activity::where('user_id', $userId)
            ->where('tipe', 'tetap')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->get();

        // PENTING: filter status aktif/null juga di sini.
        // Tanpa ini, tugas fleksibel yang gagal dijadwalkan lalu sudah
        // "diarsipkan" (status=selesai via Akhiri Minggu Ini) akan TETAP
        // ikut dibaca selamanya oleh AI di setiap generate berikutnya,
        // padahal user sudah menganggapnya selesai/dipindah ke Riwayat.
        $tugas_fleksibel = Activity::where('user_id', $userId)
            ->where('tipe', 'fleksibel')
            ->where('is_scheduled', false)
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })
            ->get()
            ->values();

        if ($tugas_fleksibel->isEmpty()) {
            $trace[] = [
                'type'  => 'empty',
                'title' => 'Tidak ada variabel (tugas fleksibel) dalam antrean domain.',
            ];
            return [
                'status'  => 'info',
                'message' => 'Tidak ada tugas baru yang perlu dijadwalkan.',
                'trace'   => $trace,
            ];
        }

        // 2b. Urutkan tugas: deadline terdekat dulu, lalu durasi terpanjang
        // (heuristik standar CSP "most constrained variable first" agar backtracking lebih efisien)
        $hari_list_ref = $this->hari_list;
        $tugas_fleksibel = $tugas_fleksibel->sort(function ($a, $b) use ($hari_list_ref) {
            $da = array_search($a->deadline, $hari_list_ref);
            $db = array_search($b->deadline, $hari_list_ref);
            $da = $da === false ? 999 : $da;
            $db = $db === false ? 999 : $db;
            if ($da !== $db) return $da <=> $db;
            return $b->durasi <=> $a->durasi;
        })->values();

        $trace[] = [
            'type'   => 'process',
            'title'  => 'Sortir Tugas Berdasarkan Deadline Terdekat & Bangun Tabel Ketersediaan Kalender',
            'detail' => 'Urutan variabel: ' . $tugas_fleksibel->map(function ($t) {
                return $t->nama_kegiatan . ' (deadline ' . $t->deadline . ', ' . $t->durasi . 'j)';
            })->implode(' → '),
        ];

        $kalender = [];
        foreach ($this->hari_list as $hari) {
            foreach ($this->jam_produktif as $jam) {
                $kalender[$hari][$jam] = null;
            }
        }

        $blokTetap = 0;
        foreach ($jadwal_tetap as $kegiatan) {
            $start_int = (int) substr($kegiatan->jam_mulai, 0, 2);
            $end_int = (int) substr($kegiatan->jam_selesai, 0, 2);
            $durasi = $end_int - $start_int;

            for ($i = 0; $i < $durasi; $i++) {
                $jam_format = sprintf("%02d:00", $start_int + $i);
                if (array_key_exists($jam_format, $kalender[$kegiatan->hari])) {
                    $kalender[$kegiatan->hari][$jam_format] = $kegiatan->id;
                    $blokTetap++;
                }
            }
        }

        $trace[] = [
            'type'   => 'process',
            'title'  => 'Tandai Slot Jadwal Tetap (Hard Constraint)',
            'detail' => $jadwal_tetap->isEmpty()
                ? 'Tidak ada jadwal tetap. Seluruh kalender masih kosong.'
                : $jadwal_tetap->count() . ' jadwal tetap mengunci ' . $blokTetap . ' slot jam di kalender.',
        ];

        // 3. Fungsi Constraint dengan Aturan Dinamis + alasan kegagalan (untuk trace)
        $isValid = function ($hari, $jam_mulai, $durasi_tugas, $deadline, &$reason = null) use (&$kalender, $settings) {

            // --- ATURAN DINAMIS 1: Maksimal 3 Kegiatan Per Hari ---
            if ($settings->maks_3_kegiatan) {
                $kegiatan_hari_ini = count(array_unique(array_filter($kalender[$hari])));
                if ($kegiatan_hari_ini + 1 > 3) {
                    $reason = 'Sudah ada 3 kegiatan di hari ' . $hari . ' (batas maksimal tercapai)';
                    return false;
                }
            }

            // Aturan Dasar (Hard Constraint)
            $hari_idx = array_search($hari, $this->hari_list);
            $dl_idx = array_search($deadline, $this->hari_list);
            if ($hari_idx > $dl_idx) {
                $reason = 'Melewati deadline (' . $deadline . ')';
                return false;
            }

            $start_int = (int) substr($jam_mulai, 0, 2);

            for ($i = 0; $i < $durasi_tugas; $i++) {
                $jam_cek = sprintf("%02d:00", $start_int + $i);
                if (!in_array($jam_cek, $this->jam_produktif)) {
                    $reason = 'Durasi tugas melewati jam operasional (' . $jam_cek . ')';
                    return false;
                }
                if ($kalender[$hari][$jam_cek] !== null) {
                    $reason = 'Slot ' . $jam_cek . ' sudah terisi (bentrok)';
                    return false;
                }
            }

            // --- ATURAN DINAMIS 2: Istirahat Minimal 1 Jam ---
            if ($settings->istirahat_1_jam) {
                $jam_sebelum = sprintf("%02d:00", $start_int - 1);
                if (in_array($jam_sebelum, $this->jam_produktif) && $kalender[$hari][$jam_sebelum] !== null) {
                    $reason = 'Tidak ada jeda istirahat sebelum jam ' . $jam_mulai;
                    return false;
                }

                $jam_setelah = sprintf("%02d:00", $start_int + $durasi_tugas);
                if (in_array($jam_setelah, $this->jam_produktif) && $kalender[$hari][$jam_setelah] !== null) {
                    $reason = 'Tidak ada jeda istirahat setelah tugas selesai';
                    return false;
                }
            }

            return true;
        };

        // --- ALGORITMA BACKTRACK (diinstrumentasi untuk merekam trace) ---
        $backtrack = function ($index) use (&$tugas_fleksibel, &$kalender, &$isValid, &$backtrack, &$trace) {
            if ($index === count($tugas_fleksibel)) {
                $trace[] = [
                    'type'  => 'success',
                    'title' => 'Semua variabel berhasil dijadwalkan!',
                ];
                return true;
            }

            $tugas = $tugas_fleksibel[$index];
            $durasi_tugas = $tugas->durasi;

            $trace[] = [
                'type'  => 'variable',
                'title' => 'Variabel: ' . $tugas->nama_kegiatan,
                'detail' => 'Durasi ' . $durasi_tugas . ' jam · Deadline sebelum/pada hari ' . $tugas->deadline,
            ];

            foreach ($this->hari_list as $hari) {
                foreach ($this->jam_produktif as $jam) {
                    $reason = null;
                    $valid = $isValid($hari, $jam, $durasi_tugas, $tugas->deadline, $reason);

                    if ($this->checkStepCount < $this->maxCheckSteps) {
                        $trace[] = [
                            'type'   => 'check',
                            'status' => $valid ? 'pass' : 'fail',
                            'title'  => $hari . ' ' . $jam,
                            'detail' => $valid ? 'Domain valid untuk ditempatkan' : $reason,
                        ];
                        $this->checkStepCount++;
                    }

                    if ($valid) {
                        $start_int = (int) substr($jam, 0, 2);
                        for ($i = 0; $i < $durasi_tugas; $i++) {
                            $jam_assign = sprintf("%02d:00", $start_int + $i);
                            $kalender[$hari][$jam_assign] = $tugas->id;
                        }
                        $jam_selesai_preview = sprintf("%02d:00", $start_int + $durasi_tugas);

                        $trace[] = [
                            'type'  => 'assign',
                            'title' => 'Tempatkan "' . $tugas->nama_kegiatan . '" di ' . $hari . ' ' . $jam . '–' . $jam_selesai_preview,
                        ];

                        if ($backtrack($index + 1)) return true;

                        for ($i = 0; $i < $durasi_tugas; $i++) {
                            $jam_unassign = sprintf("%02d:00", $start_int + $i);
                            $kalender[$hari][$jam_unassign] = null;
                        }

                        $trace[] = [
                            'type'  => 'backtrack',
                            'title' => 'Backtrack: lepas "' . $tugas->nama_kegiatan . '" dari ' . $hari . ' ' . $jam,
                            'detail' => 'Penempatan ini tidak menghasilkan solusi lengkap untuk variabel selanjutnya, dilepas dan dicoba domain lain.',
                        ];
                    }
                }
            }

            $trace[] = [
                'type'  => 'deadend',
                'title' => 'Jalan buntu untuk "' . $tugas->nama_kegiatan . '"',
                'detail' => 'Semua kandidat slot pada domain habis dicoba, tidak ada yang valid.',
            ];

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
            return [
                'status'  => 'success',
                'message' => 'AI berhasil menemukan jadwal optimal sesuai aturan preferensi!',
                'trace'   => $trace,
            ];
        }

        $trace[] = [
            'type'  => 'fail',
            'title' => 'AI gagal menemukan kombinasi jadwal yang valid',
            'detail' => 'Seluruh kemungkinan domain telah dicoba dan tidak ada solusi yang memenuhi semua constraint saat ini.',
        ];

        return [
            'status'  => 'error',
            'message' => 'AI gagal menemukan waktu luang yang mematuhi preferensi saat ini.',
            'trace'   => $trace,
        ];
    }
}