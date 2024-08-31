<?php

namespace EdLugz\Tanda;

use Illuminate\Support\ServiceProvider;

class TandaServiceProvider extends ServiceProvider
{
    /**
     * Package path to config.
     */
    const CONFIG_PATH = __DIR__.'/../config/tanda.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('tanda.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tanda.php', 'tanda');

        // Register the service the package provides.
        $this->app->singleton('tanda', function ($app) {
            return new Tanda();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['tanda'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/tanda.php' => config_path('tanda.php'),
        ], 'tanda.config');
    }
}
