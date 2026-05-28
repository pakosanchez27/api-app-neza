<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);

        $appUrl = (string) config('app.url');
        $forwardedProto = (string) request()->header('X-Forwarded-Proto', '');
        $shouldForceHttps = app()->environment('production')
            || str_starts_with($appUrl, 'https://')
            || str_contains(strtolower($forwardedProto), 'https');

        if ($shouldForceHttps) {
            URL::forceScheme('https');
        }
    }
}
