<?php

namespace App\Providers;

use App\Services\BeemSmsService;
use App\Services\NextSmsService;
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
        $this->app->singleton(BeemSmsService::class, function ($app) {
            return new BeemSmsService();
        });

        $this->app->singleton(NextSmsService::class, function($app) {
            return new NextSmsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // force https on production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
