<?php

namespace ctf0\Blazar;

use Illuminate\Support\ServiceProvider;

class BlazarServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        // config
        $this->publishes([
            __DIR__ . '/config' => config_path(),
        ], 'config');
    }

    /**
     * Register any package services.
     */
    public function register()
    {
        // commands
        $this->commands([
            Commands\BlazarFlush::class,
        ]);

        // events & listeners
        $this->app->register(BlazarEventServiceProvider::class);
        $this->app->register(\ctf0\PackageChangeLog\PackageChangeLogServiceProvider::class);
    }
}
