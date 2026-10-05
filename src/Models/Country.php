<?php

namespace FLAIRUK\Countries\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the optional countries table (see `php artisan countries:install`).
 *
 * @property int $id
 * @property string $iso_3166_2
 * @property string $iso_3166_3
 * @property string $numeric_code
 * @property string $name
 * @property string|null $full_name
 * @property string|null $capital
 * @property string|null $citizenship
 * @property string|null $currency
 * @property string|null $currency_code
 * @property string|null $currency_sub_unit
 * @property string|null $currency_symbol
 * @property int|null $currency_decimals
 * @property string|null $calling_code
 * @property string|null $region_code
 * @property string|null $sub_region_code
 * @property bool $eea
 */
class Country extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'currency_decimals' => 'integer',
            'eea' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        return config('countries.table', 'countries');
    }

    public function getConnectionName(): ?string
    {
        return $this->connection ?? config('countries.connection');
    }

    /**
     * Match an alpha-2 or alpha-3 code.
     *
     * @param  Builder<self>  $query
     */
    public function scopeCode(Builder $query, string $code): void
    {
        $code = strtoupper($code);

        $query->where(strlen($code) === 3 ? 'iso_3166_3' : 'iso_3166_2', $code);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeEea(Builder $query): void
    {
        $query->where('eea', true);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeUsingCurrency(Builder $query, string $currencyCode): void
    {
        $query->where('currency_code', strtoupper($currencyCode));
    }
}
