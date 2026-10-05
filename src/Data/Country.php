<?php

namespace FLAIRUK\Countries\Data;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class Country implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $id,
        public string $iso2,
        public string $iso3,
        public string $numericCode,
        public string $name,
        public ?string $fullName,
        public ?string $capital,
        public ?string $citizenship,
        public ?string $currency,
        public ?string $currencyCode,
        public ?string $currencySubUnit,
        public ?string $currencySymbol,
        public ?int $currencyDecimals,
        public ?string $callingCode,
        public ?string $regionCode,
        public ?string $subRegionCode,
        public bool $eea,
    ) {}

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: $row['id'],
            iso2: $row['iso_3166_2'],
            iso3: $row['iso_3166_3'],
            numericCode: $row['numeric_code'],
            name: $row['name'],
            fullName: $row['full_name'],
            capital: $row['capital'],
            citizenship: $row['citizenship'],
            currency: $row['currency'],
            currencyCode: $row['currency_code'],
            currencySubUnit: $row['currency_sub_unit'],
            currencySymbol: $row['currency_symbol'],
            currencyDecimals: $row['currency_decimals'],
            callingCode: $row['calling_code'],
            regionCode: $row['region_code'],
            subRegionCode: $row['sub_region_code'],
            eea: $row['eea'],
        );
    }

    /**
     * The flag as an emoji, e.g. "🇬🇧".
     */
    public function flagEmoji(): string
    {
        return implode('', array_map(
            fn (string $letter) => mb_chr(0x1F1E6 + ord($letter) - ord('A')),
            str_split($this->iso2),
        ));
    }

    /**
     * Public URL of the bundled PNG flag, or null if there is no image for this country.
     *
     * Requires the flags to be published: `php artisan vendor:publish --tag=countries-flags`.
     */
    public function flagUrl(): ?string
    {
        if (! is_file(__DIR__."/../../resources/flags/{$this->iso2}.png")) {
            return null;
        }

        return asset(trim(config('countries.flags_path', 'vendor/countries/flags'), '/')."/{$this->iso2}.png");
    }

    /**
     * International dialling prefix, e.g. "+44".
     */
    public function dialCode(): ?string
    {
        return $this->callingCode === null ? null : '+'.$this->callingCode;
    }

    /**
     * Column-style array matching the database table.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'iso_3166_2' => $this->iso2,
            'iso_3166_3' => $this->iso3,
            'numeric_code' => $this->numericCode,
            'name' => $this->name,
            'full_name' => $this->fullName,
            'capital' => $this->capital,
            'citizenship' => $this->citizenship,
            'currency' => $this->currency,
            'currency_code' => $this->currencyCode,
            'currency_sub_unit' => $this->currencySubUnit,
            'currency_symbol' => $this->currencySymbol,
            'currency_decimals' => $this->currencyDecimals,
            'calling_code' => $this->callingCode,
            'region_code' => $this->regionCode,
            'sub_region_code' => $this->subRegionCode,
            'eea' => $this->eea,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray() + ['flag' => $this->flagEmoji()];
    }
}
