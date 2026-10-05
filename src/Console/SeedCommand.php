<?php

namespace FLAIRUK\Countries\Console;

use FLAIRUK\Countries\Countries;
use FLAIRUK\Countries\Database\CountriesSeeder;
use FLAIRUK\Countries\Models\Country;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'countries:seed')]
class SeedCommand extends Command
{
    protected $signature = 'countries:seed
                            {--prune : Delete rows that are no longer in the dataset}';

    protected $description = 'Insert or update the countries table from the bundled dataset';

    public function handle(Countries $countries): int
    {
        $this->laravel->call([$this->laravel->make(CountriesSeeder::class), 'run']);

        if ($this->option('prune')) {
            $pruned = Country::query()->whereNotIn('id', $countries->all()->pluck('id'))->delete();
            $this->components->info("Pruned {$pruned} stale countries.");
        }

        $this->components->info("Seeded {$countries->all()->count()} countries.");

        return self::SUCCESS;
    }
}
