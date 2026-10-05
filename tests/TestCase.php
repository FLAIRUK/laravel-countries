<?php

namespace FLAIRUK\Countries\Tests;

use FLAIRUK\Countries\CountriesServiceProvider;
use FLAIRUK\Countries\Facades\Countries;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CountriesServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Countries' => Countries::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
    }
}
