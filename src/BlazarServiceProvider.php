<?php

namespace ctf0\Blazar;

use Illuminate\Foundation\AliasLoader;
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

        // packages
        $this->app->register(\Jaybizzle\LaravelCrawlerDetect\LaravelCrawlerDetectServiceProvider::class);
        AliasLoader::getInstance()->alias('Crawler', 'Jaybizzle\LaravelCrawlerDetect\Facades\LaravelCrawlerDetect');
        $this->app->register(\ctf0\PackageChangeLog\PackageChangeLogServiceProvider::class);
    }
}
