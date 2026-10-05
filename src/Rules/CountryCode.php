<?php

namespace FLAIRUK\Countries\Rules;

use Closure;
use FLAIRUK\Countries\Countries;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that the value is a known ISO 3166-1 country code.
 *
 * Accepts alpha-2 codes by default; use the named constructors for other formats.
 */
class CountryCode implements ValidationRule
{
    public const ALPHA_2 = 'alpha2';

    public const ALPHA_3 = 'alpha3';

    public const NUMERIC = 'numeric';

    /**
     * @param  list<string>  $formats
     */
    public function __construct(protected array $formats = [self::ALPHA_2]) {}

    public static function alpha2(): self
    {
        return new self([self::ALPHA_2]);
    }

    public static function alpha3(): self
    {
        return new self([self::ALPHA_3]);
    }

    public static function numeric(): self
    {
        return new self([self::NUMERIC]);
    }

    public static function any(): self
    {
        return new self([self::ALPHA_2, self::ALPHA_3, self::NUMERIC]);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = is_string($value) || is_int($value) ? strtoupper(trim((string) $value)) : null;
        $country = $value === null ? null : app(Countries::class)->find($value);

        $matches = $country !== null && (
            (in_array(self::ALPHA_2, $this->formats, true) && $value === $country->iso2)
            || (in_array(self::ALPHA_3, $this->formats, true) && $value === $country->iso3)
            || (in_array(self::NUMERIC, $this->formats, true) && ctype_digit($value))
        );

        if (! $matches) {
            $fail('The :attribute must be a valid country code.');
        }
    }
}
