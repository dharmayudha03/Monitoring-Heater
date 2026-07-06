<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // Force HTTPS for asset & form submission when accessed via HTTPS Tunnel (serveo, localtunnel, ngrok)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        } elseif (isset($_SERVER['HTTP_HOST']) && (
            str_contains($_SERVER['HTTP_HOST'], 'serveousercontent.com') ||
            str_contains($_SERVER['HTTP_HOST'], 'loca.lt') ||
            str_contains($_SERVER['HTTP_HOST'], 'ngrok') ||
            str_contains($_SERVER['HTTP_HOST'], 'pinggy')
        )) {
            URL::forceScheme('https');
        }

        // Automatically inject default secret 'irc2026' when plain 'php artisan down' is run
        $downFile = storage_path('framework/down');
        if (file_exists($downFile)) {
            $downData = json_decode(file_get_contents($downFile), true);
            if (is_array($downData) && empty($downData['secret'])) {
                $downData['secret'] = 'irc2026';
                file_put_contents($downFile, json_encode($downData));
            }
        }
    }
}
