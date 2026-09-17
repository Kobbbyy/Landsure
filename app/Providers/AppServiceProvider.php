<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        /*
         * Render terminates TLS at its proxy and forwards requests
         * to the container over plain HTTP. Without this, Laravel
         * would generate http:// URLs for assets, form actions, and
         * redirects, which causes mixed content warnings and broken
         * scripts (for example, the Leaflet map).
         *
         * Forcing https in production fixes both at once.
         */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}