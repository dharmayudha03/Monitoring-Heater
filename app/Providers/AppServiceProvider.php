<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
