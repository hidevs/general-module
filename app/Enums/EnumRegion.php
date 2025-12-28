<?php

namespace Modules\General\Enums;

use Modules\General\Contracts\Enum\EnumMethods;

enum EnumRegion: int
{
    use EnumMethods;

    case IR = 103;

    case AE = 231;

    case HU = 99;

    public static function current(): EnumRegion
    {
        return self::fromName(config('general.region.country'));
    }

    public static function country(?string $region = null): ?array
    {
        return is_null($region) ? config('general.region.countries') : (config('general.region.countries')[$region] ?? null);
    }

    public function config(): array
    {
        return self::country($this->iso2());
    }

    public function capital(): string
    {
        return self::country($this->name)['capital'];
    }

    public function capital_city_id(): string
    {
        return self::country($this->name)['capital_city_id'];
    }

    public function currency(): string
    {
        return self::country($this->name)['currency'];
    }

    public function currencyName(): string
    {
        return self::country($this->name)['currency_name'];
    }

    public function currencySymbol(): string
    {
        return self::country($this->name)['currency_symbol'];
    }

    public function emoji(): string
    {
        return self::country($this->name)['emoji'];
    }

    public function emojiU(): string
    {
        return self::country($this->name)['emojiU'];
    }

    public function id(): int
    {
        return self::country($this->name)['id'];
    }

    public function iso2(): string
    {
        return self::country($this->name)['iso2'];
    }

    public function iso3(): string
    {
        return self::country($this->name)['iso3'];
    }

    public function languages(): string
    {
        return self::country($this->name)['languages'];
    }

    public function locale(): string
    {
        return self::country($this->name)['locale'];
    }

    public function latitude(): string
    {
        return self::country($this->name)['latitude'];
    }

    public function longitude(): string
    {
        return self::country($this->name)['longitude'];
    }

    public function name(): string
    {
        return self::country($this->name)['name'];
    }

    public function native(): string
    {
        return self::country($this->name)['native'];
    }

    public function numericCode(): string
    {
        return self::country($this->name)['numeric_code'];
    }

    public function phoneCode(): string
    {
        return self::country($this->name)['phone_code'];
    }

    public function timezones(): array
    {
        return self::country($this->name)['timezones'];
    }

    public function tld(): string
    {
        return self::country($this->name)['tld'];
    }

    public function translations(): array
    {
        return self::country($this->name)['translations'];
    }
}
