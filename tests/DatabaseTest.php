<?php

namespace FLAIRUK\Countries\Tests;

use FLAIRUK\Countries\Database\CountriesSeeder;
use FLAIRUK\Countries\Facades\Countries;
use FLAIRUK\Countries\Models\Country;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class DatabaseTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    #[Test]
    public function the_seed_command_fills_the_table_and_is_idempotent(): void
    {
        $this->artisan('countries:seed')->assertSuccessful();
        $this->artisan('countries:seed')->assertSuccessful();

        $this->assertSame(Countries::all()->count(), Country::count());
        $this->assertSame('United Kingdom', Country::code('gbr')->first()->name);
        $this->assertSame(30, Country::eea()->count());
        $this->assertTrue(Country::code('gb')->first()->eea === false);
        $this->assertTrue(Country::usingCurrency('eur')->where('iso_3166_2', 'HR')->exists());
    }

    #[Test]
    public function the_seeder_can_be_called_from_an_application_seeder(): void
    {
        $this->seed(CountriesSeeder::class);

        $this->assertSame(Countries::all()->count(), Country::count());
    }

    #[Test]
    public function prune_removes_rows_that_are_not_in_the_dataset(): void
    {
        Country::create(['id' => 999, 'iso_3166_2' => 'YU', 'iso_3166_3' => 'YUG', 'numeric_code' => '891', 'name' => 'Yugoslavia']);

        $this->artisan('countries:seed', ['--prune' => true])->assertSuccessful();

        $this->assertNull(Country::find(999));
        $this->assertSame(Countries::all()->count(), Country::count());
    }

    #[Test]
    public function the_table_name_is_configurable(): void
    {
        config(['countries.table' => 'iata_countries']);
        (require __DIR__.'/../database/migrations/create_countries_table.php')->up();

        $this->artisan('countries:seed')->assertSuccessful();

        $this->assertTrue(Schema::hasTable('iata_countries'));
        $this->assertSame(Countries::all()->count(), Country::count());
    }

    #[Test]
    public function install_publishes_the_config_and_a_timestamped_migration(): void
    {
        $migrations = database_path('migrations');
        File::delete(File::glob($migrations.'/*_create_countries_table.php'));
        File::delete(config_path('countries.php'));

        $this->artisan('countries:install')
            ->expectsConfirmation('Run the migration and seed the countries table now?', 'no')
            ->assertSuccessful();

        $this->assertFileExists(config_path('countries.php'));
        $this->assertCount(1, File::glob($migrations.'/*_create_countries_table.php'));

        File::delete(File::glob($migrations.'/*_create_countries_table.php'));
        File::delete(config_path('countries.php'));
    }
}
