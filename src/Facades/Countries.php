<?php

namespace FLAIRUK\Countries\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> all()
 * @method static \FLAIRUK\Countries\Data\Country|null find(string|int $code)
 * @method static \FLAIRUK\Countries\Data\Country findOrFail(string|int $code)
 * @method static \FLAIRUK\Countries\Data\Country|null findByName(string $name)
 * @method static bool exists(string|int $code)
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> inRegion(string $code)
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> eea()
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> usingCurrency(string $currencyCode)
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> withCallingCode(string $callingCode)
 * @method static \Illuminate\Support\Collection<string, \FLAIRUK\Countries\Data\Country> search(string $term)
 * @method static \Illuminate\Support\Collection<int|string, string> options(string $key = 'iso2', string $label = 'name')
 * @method static list<string> currencies()
 * @method static list<string> codes()
 *
 * @see \FLAIRUK\Countries\Countries
 */
class Countries extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FLAIRUK\Countries\Countries::class;
    }
}
