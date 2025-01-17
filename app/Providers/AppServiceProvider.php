<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MSG91Service;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MSG91Service::class, function ($app) {
            return new MSG91Service();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
