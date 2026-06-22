<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'istirahat_1_jam' => true,
                'maks_3_kegiatan' => false,
                'waktu_produktif' => true,
                'no_overlap_kuliah' => true,
                'no_overlap_all' => true,
                'strict_deadline' => true,
                'prioritas_kuliah' => true,
            ]
        );

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::where('user_id', Auth::id())->first();

        $setting->update([
            'istirahat_1_jam' => $request->has('istirahat_1_jam'),
            'maks_3_kegiatan' => $request->has('maks_3_kegiatan'),
            'waktu_produktif' => $request->has('waktu_produktif'),
            'no_overlap_kuliah' => $request->has('no_overlap_kuliah'),
            'no_overlap_all' => $request->has('no_overlap_all'),
            'strict_deadline' => $request->has('strict_deadline'),
            'prioritas_kuliah' => $request->has('prioritas_kuliah'),
        ]);

        return back()->with('success', 'Preferensi AI berhasil diperbarui!');
    }

    public function updateApi(Request $request)
    {
        $setting = Setting::where('user_id', Auth::id())->first();

        $setting->update([
            'istirahat_1_jam' => $request->istirahat_1_jam ?? false,
            'maks_3_kegiatan' => $request->maks_3_kegiatan ?? false,
            'waktu_produktif' => $request->waktu_produktif ?? false,
            'no_overlap_kuliah' => $request->no_overlap_kuliah ?? false,
            'no_overlap_all' => $request->no_overlap_all ?? false,
            'strict_deadline' => $request->strict_deadline ?? false,
            'prioritas_kuliah' => $request->prioritas_kuliah ?? false,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Preferensi AI berhasil diperbarui!'
        ], 200);
    }

    public function getApi()
    {
        $setting = Setting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'istirahat_1_jam' => true,
                'maks_3_kegiatan' => false,
                'waktu_produktif' => true,
                'no_overlap_kuliah' => true,
                'no_overlap_all' => true,
                'strict_deadline' => true,
                'prioritas_kuliah' => true,
            ]
        );

        return response()->json(['success' => true, 'data' => $setting], 200);
    }
}