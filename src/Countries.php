<?php

namespace FLAIRUK\Countries;

use FLAIRUK\Countries\Data\Country;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;

/**
 * In-memory lookup of ISO 3166 countries.
 *
 * The dataset is loaded lazily on first use and kept for the lifetime of the
 * instance (bound as a singleton), so lookups never touch the database.
 */
class Countries
{
    /** @var Collection<string, Country>|null */
    protected ?Collection $countries = null;

    /** @var array<string, string>|null alpha-3 and numeric codes => alpha-2 */
    protected ?array $aliases = null;

    public function __construct(
        protected string $path = __DIR__.'/../data/countries.php',
    ) {}

    /**
     * Every country, keyed by ISO 3166-1 alpha-2 code.
     *
     * @return Collection<string, Country>
     */
    public function all(): Collection
    {
        return $this->countries ??= collect(require $this->path)
            ->mapWithKeys(fn (array $row) => [$row['iso_3166_2'] => Country::fromArray($row)]);
    }

    /**
     * Find a country by alpha-2 ("GB"), alpha-3 ("GBR") or numeric ("826") code.
     */
    public function find(string|int $code): ?Country
    {
        $code = strtoupper(trim((string) $code));

        if (ctype_digit($code)) {
            $code = str_pad($code, 3, '0', STR_PAD_LEFT);
        }

        return $this->all()->get($this->aliases()[$code] ?? $code);
    }

    /**
     * @throws ItemNotFoundException
     */
    public function findOrFail(string|int $code): Country
    {
        return $this->find($code) ?? throw new ItemNotFoundException("Unknown country code [{$code}].");
    }

    public function findByName(string $name): ?Country
    {
        return $this->all()->first(fn (Country $country) => strcasecmp($country->name, trim($name)) === 0
            || strcasecmp((string) $country->fullName, trim($name)) === 0);
    }

    public function exists(string|int $code): bool
    {
        return $this->find($code) !== null;
    }

    /**
     * Countries in a UN M49 region or sub-region, e.g. "150" (Europe) or "154" (Northern Europe).
     *
     * @return Collection<string, Country>
     */
    public function inRegion(string $code): Collection
    {
        return $this->all()->filter(fn (Country $country) => $country->regionCode === $code || $country->subRegionCode === $code);
    }

    /**
     * Members of the European Economic Area.
     *
     * @return Collection<string, Country>
     */
    public function eea(): Collection
    {
        return $this->all()->where('eea', true);
    }

    /**
     * Countries using an ISO 4217 currency, e.g. "EUR".
     *
     * @return Collection<string, Country>
     */
    public function usingCurrency(string $currencyCode): Collection
    {
        return $this->all()->where('currencyCode', strtoupper($currencyCode));
    }

    /**
     * Countries sharing an international calling code, e.g. "1" or "+44".
     *
     * @return Collection<string, Country>
     */
    public function withCallingCode(string $callingCode): Collection
    {
        return $this->all()->where('callingCode', ltrim(trim($callingCode), '+'));
    }

    /**
     * Case-insensitive match against codes, name or full name; exact code matches are ranked first.
     *
     * @return Collection<string, Country>
     */
    public function search(string $term): Collection
    {
        $term = trim($term);

        if ($term === '') {
            return new Collection;
        }

        $exact = $this->find($term);

        return $this->all()
            ->filter(fn (Country $country) => $country === $exact
                || mb_stripos($country->name, $term) !== false
                || mb_stripos((string) $country->fullName, $term) !== false)
            ->sortBy(fn (Country $country) => $country === $exact ? 0 : 1);
    }

    /**
     * Key/label pairs for a <select>, sorted by label.
     *
     * @return Collection<int|string, string>
     */
    public function options(string $key = 'iso2', string $label = 'name'): Collection
    {
        return $this->all()->sortBy($label, SORT_NATURAL | SORT_FLAG_CASE)->pluck($label, $key);
    }

    /**
     * Distinct ISO 4217 currency codes in use, sorted.
     *
     * @return list<string>
     */
    public function currencies(): array
    {
        return $this->all()->pluck('currencyCode')->filter()->unique()->sort()->values()->all();
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        return $this->all()->keys()->all();
    }

    /**
     * @return array<string, string>
     */
    protected function aliases(): array
    {
        if ($this->aliases === null) {
            $this->aliases = [];

            foreach ($this->all() as $country) {
                $this->aliases[$country->iso3] = $country->iso2;
                $this->aliases[$country->numericCode] = $country->iso2;
            }
        }

        return $this->aliases;
    }
}
