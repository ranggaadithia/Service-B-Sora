<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CollectorRegistry::class, function () {
            return new CollectorRegistry(new APC());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
