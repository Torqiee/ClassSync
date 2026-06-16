<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        // Cari pengaturan user, jika belum ada, buatkan defaultnya
        $setting = Setting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'istirahat_1_jam' => true,
                'maks_3_kegiatan' => false,
                'waktu_produktif' => true,
            ]
        );

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::where('user_id', Auth::id())->first();

        // Checkbox di HTML hanya mengirim data jika dicentang (aktif)
        $setting->update([
            'istirahat_1_jam' => $request->has('istirahat_1_jam'),
            'maks_3_kegiatan' => $request->has('maks_3_kegiatan'),
            'waktu_produktif' => $request->has('waktu_produktif'),
        ]);

        return back()->with('success', 'Preferensi AI berhasil diperbarui!');
    }

    public function updateApi(Request $request)
    {
        $setting = Setting::where('user_id', Auth::id())->first();

        // API Flutter mengirim data dalam bentuk JSON, 
        // jadi kita ambil nilainya langsung (bukan lewat $request->has())
        $setting->update([
            'istirahat_1_jam' => $request->istirahat_1_jam ?? false,
            'maks_3_kegiatan' => $request->maks_3_kegiatan ?? false,
            'waktu_produktif' => $request->waktu_produktif ?? false,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Preferensi AI berhasil diperbarui!'
        ], 200);
    }

    public function getApi()
    {
        // Cari pengaturan user, jika belum ada, buatkan defaultnya
        $setting = Setting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'istirahat_1_jam' => true,
                'maks_3_kegiatan' => false,
                'waktu_produktif' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $setting
        ], 200);
    }
}