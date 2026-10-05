<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="art/logo-dark.svg">
    <img src="art/logo-light.svg" alt="Laravel Countries" width="420">
  </picture>
</p>

<h2 align="center">
  <a href="https://www.php.net/" target="_blank"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.2+"></a>&nbsp;
  <a href="https://laravel.com/docs/" target="_blank"><img src="https://img.shields.io/badge/Laravel-12%20%7C%2013-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel 12 or 13"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-countries/actions/workflows/tests.yml" target="_blank"><img src="https://img.shields.io/badge/Lint-%E2%9C%93-2EA043?style=flat&logo=githubactions&logoColor=white" alt="Lint"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-countries/actions/workflows/tests.yml" target="_blank"><img src="https://img.shields.io/badge/Tests-%E2%9C%93-2EA043?style=flat&logo=githubactions&logoColor=white" alt="Tests"></a>&nbsp;
  <a href="https://packagist.org/packages/flairuk/laravel-countries" target="_blank"><img src="https://img.shields.io/packagist/dt/flairuk/laravel-countries?style=flat&logo=packagist&logoColor=white&label=Downloads&color=F28D1A" alt="Downloads on Packagist"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-countries/blob/master/LICENSE" target="_blank"><img src="https://img.shields.io/github/license/FLAIRUK/laravel-countries?style=flat&label=License&color=3DA639" alt="MIT licence"></a>&nbsp;
  <a href="https://www.iso.org/iso-3166-country-codes.html" target="_blank"><img src="https://img.shields.io/badge/Data-ISO%203166-15803D?style=flat" alt="ISO 3166"></a>&nbsp;
  <br>&nbsp;
</h2>

**Laravel Countries** — All 249 ISO 3166 countries for Laravel 12 and 13. Each country includes:

- alpha-2, alpha-3 and numeric codes
- currency (ISO 4217 code, symbol, sub-unit, decimals)
- international calling code
- capital and citizenship
- UN M49 region and sub-region
- EEA membership
- flags, as emoji and as bundled PNGs

What the package provides:

- **No database required.** Look countries up through a facade backed by an in-memory dataset.
- **Typed results.** Every lookup returns readonly `Country` objects in Laravel collections keyed by alpha-2 code.
- **Validation rule.** `CountryCode` accepts alpha-2, alpha-3 and/or numeric codes.
- **Optional table.** Publish a migration and seed a `countries` table when other tables need to reference countries.

<p align="center">
  📦&nbsp;<a href="#-installation">Installation</a> ·
  🚀&nbsp;<a href="#-usage">Usage</a> ·
  💾&nbsp;<a href="#-database-table-optional">Database table</a> ·
  🔄&nbsp;<a href="#-upgrading-from-dev-master">Upgrading</a>
</p>

<br><br>

## 📦 Installation

```bash
composer require flairuk/laravel-countries
```

Laravel discovers the service provider and the `Countries` facade automatically.

<br><br>

## 🚀 Usage

```php
use FLAIRUK\Countries\Facades\Countries;

$uk = Countries::find('GB');     // also 'gbr', '826' or 826
$uk->name;                       // "United Kingdom"
$uk->iso3;                       // "GBR"
$uk->currencyCode;               // "GBP"
$uk->currencySymbol;             // "£"
$uk->dialCode();                 // "+44"
$uk->flagEmoji();                // "🇬🇧"
$uk->flagUrl();                  // "https://app.test/vendor/countries/flags/GB.png"

Countries::findOrFail('XX');     // throws ItemNotFoundException
Countries::findByName('France');
Countries::exists('DEU');        // true

Countries::all();                // Collection<string, Country> keyed by alpha-2
Countries::eea();                // the 30 EEA members
Countries::usingCurrency('EUR');
Countries::withCallingCode('+1');
Countries::inRegion('150');      // UN M49 region (Europe) or sub-region ('154' = Northern Europe)
Countries::search('kingdom');    // name, full name or exact code
Countries::currencies();         // ['AED', 'AFN', ...]
```

### Select options

```php
Countries::options();                    // ['AF' => 'Afghanistan', ...] sorted by name
Countries::options('iso3');              // ['AFG' => 'Afghanistan', ...]
Countries::options('iso2', 'citizenship');
```

### Validation

```php
use FLAIRUK\Countries\Rules\CountryCode;

$request->validate([
    'country' => ['required', new CountryCode],          // alpha-2 (default)
    'nationality' => ['required', CountryCode::alpha3()],
    'origin' => ['required', CountryCode::any()],        // alpha-2, alpha-3 or numeric
]);
```

### Flags

`flagEmoji()` works everywhere and needs no assets. To use the PNG flags (30×20), publish them to `public/vendor/countries/flags`:

```bash
php artisan vendor:publish --tag=countries-flags
```

`flagUrl()` returns `null` for the few newer territories without a bundled image: AX, BL, BQ, CW, GG, IM, JE, MF, RS, SS and SX.

<br><br>

## 💾 Database table (optional)

```bash
php artisan countries:install         # publish config + migration, then migrate and seed
php artisan countries:seed            # insert / update (safe to re-run)
php artisan countries:seed --prune    # also delete rows no longer in the dataset
```

You can also call the seeder from your own `DatabaseSeeder`:

```php
$this->call(\FLAIRUK\Countries\Database\CountriesSeeder::class);
```

Query the table through the bundled Eloquent model:

```php
use FLAIRUK\Countries\Models\Country;

Country::code('GB')->first();       // alpha-2 or alpha-3
Country::eea()->orderBy('name')->get();
Country::usingCurrency('EUR')->pluck('name');
```

The table name and connection come from `COUNTRIES_TABLE` and `COUNTRIES_DB_CONNECTION`, or from the published config. The primary key `id` is the ISO 3166 numeric code.

<br><br>

## 🔄 Upgrading from dev-master

Version 1.0 is a rewrite. Breaking changes:

| dev-master | 1.0 |
| --- | --- |
| Facade `FLAIRUK\Countries\CountriesFacade` | `FLAIRUK\Countries\Facades\Countries` |
| `Countries::getList($sort)` (array) | `Countries::all()->sortBy($sort)` (Collection of `Country`) |
| `Countries::getOne($id)` | `Countries::find($id)` (numeric code) |
| `Countries::getListForSelect($display)` (keyed by id) | `Countries::options('id', $display)` |
| `php artisan countries:migration` | `php artisan countries:install` / `countries:seed` |
| Config key `countries.table_name` | `countries.table` |
| Keys `country-code`, `region-code`, `sub-region-code` | `numeric_code`, `region_code`, `sub_region_code` |
| Column `country_code` | `numeric_code` |
| Column `flag` (`"GB.png"`) | removed. Use `flagUrl()` / `flagEmoji()` |
| Flags in `src/flags` | `resources/flags`, publishable with `--tag=countries-flags` |

Row `id`s are unchanged. If you have an existing table, rename the column before re-seeding:

```php
Schema::table('countries', function (Blueprint $table) {
    $table->renameColumn('country_code', 'numeric_code');
    $table->dropColumn('flag');
});
```

### Data corrections in 1.0

- **EEA membership:** the United Kingdom has left (Brexit). Iceland, Liechtenstein and Norway have been added. The list now has 30 members.
- **Euro adoption:** Croatia (2023) and Bulgaria (2026) now use the euro. Euro symbols are fixed for Estonia, Latvia, Lithuania, Malta, Slovakia, Cyprus, Åland, Saint Barthélemy and Saint Martin.
- **Re-denominated currencies:** BYR → BYN, MRO → MRU, STD → STN, SLL → SLE, VEF → VES, ZWL → ZWG.
- **Sterling issues:** Guernsey, Jersey and the Isle of Man use the ISO code `GBP`. Their old codes (GGP, JEP, IMP) are not ISO 4217 codes.
- **Names:** Eswatini, North Macedonia, Czechia, Türkiye and Cabo Verde.
- **Formatting:** whitespace is trimmed and empty values are `null`.

<br><br>

## 🧪 Testing

```bash
composer test
```

<br><br>

## 📄 License

MIT. See [LICENSE](LICENSE).
