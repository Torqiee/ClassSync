<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();

            // 1. Cek apakah user dengan email ini sudah ada di database
            $user = \App\Models\User::where('email', $googleUser->email)->first();

            if ($user) {
                // 2. JIKA USER SUDAH ADA: 
                // Cukup perbarui google_id-nya saja (jika sebelumnya dia daftar manual).
                // YANG PALING PENTING: Jangan pernah menyentuh kolom 'password' di sini!
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar'    => $googleUser->avatar, // Opsional jika kamu menyimpan foto profil
                    ]);
                }
                
                // Langsung loginkan
                \Illuminate\Support\Facades\Auth::login($user, true); // true = otomatis Remember Me
            } else {
                // 3. JIKA USER BELUM ADA SAMA SEKALI (Pengguna Baru):
                // Buatkan akun baru dengan password acak yang kuat
                $newUser = \App\Models\User::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                    'password'  => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24))
                ]);
                
                \Illuminate\Support\Facades\Auth::login($newUser, true);
            }

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal masuk menggunakan Google. Silakan coba lagi.');
        }
    }
}