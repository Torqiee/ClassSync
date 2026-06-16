<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        // Paksa Laravel menggunakan HTTPS saat diakses via ngrok
        if (env('APP_ENV') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}