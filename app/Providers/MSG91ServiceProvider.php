<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MSG91ServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('msg91', function ($app) {
            return new MSG91Service(config('services.msg91.auth_key'));
        });
    }
}
