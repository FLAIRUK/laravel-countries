<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | The in-memory lookup API (the Countries facade) works without a database.
    | These settings only apply if you publish the migration and seed the
    | countries into a table, e.g. so other tables can reference them.
    |
    */

    'table' => env('COUNTRIES_TABLE', 'countries'),

    'connection' => env('COUNTRIES_DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Flags
    |--------------------------------------------------------------------------
    |
    | Public path the PNG flags are published to with
    | `php artisan vendor:publish --tag=countries-flags`. Used by Country::flagUrl().
    |
    */

    'flags_path' => 'vendor/countries/flags',

];
