<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AiSchedulerService;

class ActivityController extends Controller
{
    // Menampilkan halaman form
    public function create()
    {
        return view('activities.create');
    }

    // Menyimpan data ke MongoDB
    public function store(Request $request)
    {
        // 1. Validasi format input dasar
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'kategori'      => 'required|string',
            'tipe'          => 'required|in:tetap,fleksibel',
        ]);

        // 2. CEK DUPLIKASI (SATPAM SISTEM)
        $isDuplicate = Activity::where('user_id', Auth::id())
            ->where('nama_kegiatan', $request->nama_kegiatan)
            ->where('tipe', $request->tipe)
            ->where(function($query) use ($request) {
                if ($request->tipe === 'tetap') {
                    // Jika jadwal tetap, cek kesamaan hari dan jam
                    $query->where('hari', $request->hari)
                          ->where('jam_mulai', $request->jam_mulai)
                          ->where('jam_selesai', $request->jam_selesai);
                } else {
                    // Jika tugas fleksibel, cek kesamaan deadline dan durasi
                    $query->where('deadline', $request->deadline)
                          ->where('durasi', $request->durasi);
                }
            })
            ->exists();

        // Jika terdeteksi duplikat, tolak dan kembalikan ke halaman form dengan pesan error
        if ($isDuplicate) {
            return back()
                ->withInput()
                ->with('error', 'Gagal: Aktivitas dengan detail yang sama persis sudah ada di jadwalmu!');
        }

        // 3. Jika aman dari duplikasi, simpan ke database
        $activity = new Activity();
        $activity->user_id = Auth::id();
        $activity->nama_kegiatan = $request->nama_kegiatan;
        $activity->kategori = $request->kategori;
        $activity->tipe = $request->tipe;
        // Set status bawaan untuk MongoDB
        $activity->status = 'aktif'; 

        if ($request->tipe === 'tetap') {
            $activity->hari = $request->hari;
            $activity->jam_mulai = $request->jam_mulai;
            $activity->jam_selesai = $request->jam_selesai;
            $activity->is_scheduled = true; // Jadwal paten langsung berstatus terjadwal
        } else {
            $activity->deadline = $request->deadline;
            $activity->durasi = (int) $request->durasi;
            $activity->is_scheduled = false; // Tugas fleksibel menunggu AI
        }

        $activity->save();

        return redirect()->route('dashboard')->with('success', 'Aktivitas berhasil ditambahkan!');
    }

    // Menampilkan halaman Edit
    public function edit($id)
    {
        $activity = Activity::findOrFail($id);
        
        // Pastikan hanya pemilik jadwal yang bisa mengedit
        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('activities.edit', compact('activity'));
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'kategori'      => 'required|string',
        ]);

        // CEK DUPLIKASI SAAT EDIT
        $isDuplicate = Activity::where('user_id', Auth::id())
            ->where('id', '!=', $id) 
            ->where('nama_kegiatan', $request->nama_kegiatan)
            ->where('tipe', $activity->tipe)
            ->where(function($query) use ($request, $activity) {
                if ($activity->tipe === 'tetap') {
                    $query->where('hari', $request->hari)
                          ->where('jam_mulai', $request->jam_mulai)
                          ->where('jam_selesai', $request->jam_selesai);
                } else {
                    $query->where('deadline', $request->deadline)
                          ->where('durasi', $request->durasi);
                }
            })
            ->exists();

        if ($isDuplicate) {
            return back()->withInput()->with('error', 'Gagal: Perubahan ini membuat jadwal bentrok dengan jadwal lain!');
        }

        // Lanjut update data jika aman
        $activity->nama_kegiatan = $request->nama_kegiatan;
        $activity->kategori = $request->kategori;

        if ($activity->tipe === 'tetap') {
            $activity->hari = $request->hari;
            $activity->jam_mulai = $request->jam_mulai;
            $activity->jam_selesai = $request->jam_selesai;
        } else {
            $activity->deadline = $request->deadline;
            $activity->durasi = (int) $request->durasi;
            $activity->is_scheduled = false; 
            $activity->hari = null;
            $activity->jam_mulai = null;
            $activity->jam_selesai = null;
        }

        $activity->save();

        return redirect()->route('dashboard')->with('success', 'Aktivitas berhasil diperbarui!');
    }

    // Menghapus data
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $activity->delete();

        return redirect()->route('dashboard')->with('success', 'Aktivitas berhasil dihapus!');
    }

    public function generateAI(AiSchedulerService $aiService)
    {
        $result = $aiService->generateSchedule();

        // Jika tidak ada trace berarti tidak ada proses untuk divisualisasikan
        // (misal: tidak ada tugas fleksibel sama sekali), cukup balik ke dashboard.
        if (empty($result['trace']) || count($result['trace']) <= 1) {
            // Bersihkan juga jejak visualisasi lama agar tidak nyangkut/basi
            session()->forget(['ai_trace', 'ai_trace_status', 'ai_trace_message']);
            return redirect()->route('dashboard')->with($result['status'], $result['message']);
        }

        // Simpan trace di session agar bisa dibaca halaman visualisasi
        session([
            'ai_trace'         => $result['trace'],
            'ai_trace_status'  => $result['status'],
            'ai_trace_message' => $result['message'],
        ]);

        return redirect()->route('activities.visualize');
    }

    // Menampilkan halaman visualisasi proses backtracking AI
    public function visualize()
    {
        $trace = session('ai_trace');

        if (empty($trace)) {
            return redirect()->route('dashboard')
                ->with('info', 'Belum ada data proses untuk divisualisasikan. Silakan klik "Generate Jadwal" terlebih dahulu.');
        }

        return view('activities.visualize', [
            'trace'   => $trace,
            'status'  => session('ai_trace_status', 'info'),
            'message' => session('ai_trace_message', ''),
        ]);
    }

    // ==========================================
    // FITUR BARU: HISTORY & ARCHIVE
    // ==========================================

    public function history()
    {
        // Mengambil semua aktivitas yang sudah berstatus 'selesai'
        $historyActivities = Activity::where('user_id', Auth::id())
                            ->where('status', 'selesai')
                            ->orderBy('diselesaikan_pada', 'desc')
                            ->get();

        return view('activities.history', compact('historyActivities'));
    }

    public function archiveWeek()
    {
        // Memindahkan HANYA tugas fleksibel ke halaman History.
        Activity::where('user_id', Auth::id())
                ->where('tipe', 'fleksibel')
                ->where(function($query) {
                    $query->where('status', 'aktif')
                          ->orWhereNull('status'); // Jaga-jaga untuk data lama
                })
                ->update([
                    'status' => 'selesai',
                    'diselesaikan_pada' => now()
                ]);

        // Reset visualisasi AI: hapus jejak (trace) proses backtracking minggu
        // sebelumnya dari session, supaya halaman Visualisasi AI tidak lagi
        // menampilkan proses lama yang sudah tidak relevan.
        session()->forget(['ai_trace', 'ai_trace_status', 'ai_trace_message']);

        return back()->with('success', 'Kalender di-refresh! Semua tugas fleksibel minggu ini telah dipindahkan ke Riwayat, dan visualisasi AI telah direset.');
    }
}