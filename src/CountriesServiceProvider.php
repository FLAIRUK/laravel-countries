<?php

namespace FLAIRUK\Countries;

use Illuminate\Support\ServiceProvider;

class CountriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/countries.php', 'countries');

        $this->app->singleton(Countries::class);
        $this->app->alias(Countries::class, 'countries');
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/countries.php' => config_path('countries.php'),
        ], 'countries-config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'countries-migrations');

        $this->publishes([
            __DIR__.'/../resources/flags' => public_path(config('countries.flags_path', 'vendor/countries/flags')),
        ], ['countries-flags', 'public']);

        $this->commands([
            Console\InstallCommand::class,
            Console\SeedCommand::class,
        ]);
    }
}
