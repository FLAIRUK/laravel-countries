<?php

namespace FLAIRUK\Countries\Tests;

use FLAIRUK\Countries\Countries as CountriesRepository;
use FLAIRUK\Countries\Data\Country;
use FLAIRUK\Countries\Facades\Countries;
use FLAIRUK\Countries\Rules\CountryCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;
use PHPUnit\Framework\Attributes\Test;

class CountriesTest extends TestCase
{
    #[Test]
    public function it_resolves_a_singleton_through_the_facade_and_alias(): void
    {
        $this->assertSame(app(CountriesRepository::class), app('countries'));
        $this->assertInstanceOf(CountriesRepository::class, Countries::getFacadeRoot());
    }

    #[Test]
    public function the_dataset_is_well_formed(): void
    {
        $countries = Countries::all();

        $this->assertCount(249, $countries);
        $this->assertContainsOnlyInstancesOf(Country::class, $countries);

        foreach (['id', 'iso3', 'numericCode'] as $field) {
            $this->assertSame(249, $countries->pluck($field)->unique()->count(), "{$field} must be unique");
        }

        $countries->each(function (Country $country, string $code) {
            $this->assertSame($code, $country->iso2);
            $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $country->iso2);
            $this->assertMatchesRegularExpression('/^[A-Z]{3}$/', $country->iso3);
            $this->assertMatchesRegularExpression('/^\d{3}$/', $country->numericCode);
            $this->assertSame((int) $country->numericCode, $country->id);
            $this->assertTrue($country->currencyCode === null || preg_match('/^[A-Z]{3}$/', $country->currencyCode) === 1);
        });
    }

    #[Test]
    public function it_finds_countries_by_alpha2_alpha3_or_numeric_code(): void
    {
        $uk = Countries::find('gb');

        $this->assertSame('United Kingdom', $uk->name);
        $this->assertSame($uk, Countries::find('GBR'));
        $this->assertSame($uk, Countries::find('826'));
        $this->assertSame($uk, Countries::find(826));
        $this->assertSame('AF', Countries::find(4)->iso2);
        $this->assertNull(Countries::find('XX'));
        $this->assertTrue(Countries::exists('gbr'));
        $this->assertFalse(Countries::exists('XXX'));
    }

    #[Test]
    public function find_or_fail_throws_for_unknown_codes(): void
    {
        $this->expectException(ItemNotFoundException::class);

        Countries::findOrFail('XX');
    }

    #[Test]
    public function it_finds_by_name(): void
    {
        $this->assertSame('FR', Countries::findByName('france')->iso2);
        $this->assertSame('GB', Countries::findByName('United Kingdom of Great Britain and Northern Ireland')->iso2);
        $this->assertNull(Countries::findByName('Atlantis'));
    }

    #[Test]
    public function the_eea_has_thirty_members_and_excludes_the_uk(): void
    {
        $eea = Countries::eea();

        $this->assertCount(30, $eea);
        $this->assertFalse($eea->has('GB'));
        $this->assertTrue($eea->has('NO'));
        $this->assertTrue($eea->has('DE'));
    }

    #[Test]
    public function it_filters_by_currency_calling_code_and_region(): void
    {
        $euro = Countries::usingCurrency('eur');

        $this->assertTrue($euro->has('HR'));
        $this->assertTrue($euro->has('BG'));
        $this->assertTrue($euro->every(fn (Country $c) => $c->currencySymbol === '€'));
        $this->assertTrue(Countries::withCallingCode('+1')->has('US'));
        $this->assertTrue(Countries::withCallingCode('1')->has('CA'));
        $this->assertTrue(Countries::inRegion('150')->has('FR'));
        $this->assertTrue(Countries::inRegion('154')->has('GB'));
        $this->assertFalse(Countries::inRegion('154')->has('FR'));
        $this->assertContains('GBP', Countries::currencies());
        $this->assertNotContains('HRK', Countries::currencies());
    }

    #[Test]
    public function it_searches_names_and_codes(): void
    {
        $this->assertSame('DE', Countries::search('deu')->first()->iso2);
        $this->assertTrue(Countries::search('kingdom')->has('GB'));
        $this->assertCount(0, Countries::search(' '));
    }

    #[Test]
    public function it_builds_select_options(): void
    {
        $this->assertSame('United Kingdom', Countries::options()['GB']);
        $this->assertSame('United Kingdom', Countries::options('iso3')['GBR']);
        $this->assertSame('Afghanistan', Countries::options()->first());
    }

    #[Test]
    public function countries_expose_flags_and_dial_codes(): void
    {
        $uk = Countries::find('GB');

        $this->assertSame('🇬🇧', $uk->flagEmoji());
        $this->assertSame('+44', $uk->dialCode());
        $this->assertStringEndsWith('/vendor/countries/flags/GB.png', $uk->flagUrl());
        $this->assertNull(Countries::find('SS')->flagUrl());
        $this->assertSame('🇬🇧', json_decode(json_encode($uk), true)['flag']);
    }

    #[Test]
    public function the_validation_rule_respects_the_requested_formats(): void
    {
        $passes = fn ($value, CountryCode $rule) => Validator::make(['c' => $value], ['c' => $rule])->passes();

        $this->assertTrue($passes('gb', new CountryCode));
        $this->assertFalse($passes('GBR', new CountryCode));
        $this->assertTrue($passes('GBR', CountryCode::alpha3()));
        $this->assertFalse($passes('GB', CountryCode::alpha3()));
        $this->assertTrue($passes('826', CountryCode::numeric()));
        $this->assertTrue($passes(826, CountryCode::any()));
        $this->assertFalse($passes('XX', CountryCode::any()));
        $this->assertFalse($passes(['GB'], CountryCode::any()));
    }
}
