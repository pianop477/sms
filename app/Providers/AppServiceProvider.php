<?php

namespace App\Providers;

use App\Services\FinanceTokenService;
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

        $this->app->singleton(FinanceTokenService::class, function ($app) {
            return new FinanceTokenService();
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
