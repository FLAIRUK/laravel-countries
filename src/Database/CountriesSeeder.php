<?php

namespace FLAIRUK\Countries\Database;

use FLAIRUK\Countries\Countries;
use FLAIRUK\Countries\Data\Country as CountryData;
use FLAIRUK\Countries\Models\Country;
use Illuminate\Database\Seeder;

/**
 * Upserts the country dataset into the countries table. Safe to run repeatedly.
 */
class CountriesSeeder extends Seeder
{
    public function run(Countries $countries): void
    {
        $rows = $countries->all()->map(fn (CountryData $country) => $country->toArray())->values();

        $columns = array_values(array_diff(array_keys($rows->first()), ['id']));

        $rows->chunk(50)->each(fn ($chunk) => Country::query()->upsert($chunk->values()->all(), ['id'], $columns));
    }
}
