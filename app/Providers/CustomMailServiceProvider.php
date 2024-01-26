<?php

namespace App\Providers;

use App\Services\CustomTransportManager;
use Illuminate\Support\ServiceProvider;

class CustomMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function registerSwiftTransport(): void
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new CustomTransportManager($app);
        });
    }
}
