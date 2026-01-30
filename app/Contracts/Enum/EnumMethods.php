<?php

namespace Modules\General\Contracts\Enum;

use Illuminate\Validation\ValidationException;

trait EnumMethods
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromName(string $name): static
    {
        return constant(static::class.'::'.strtoupper($name));
    }

    public static function throwFrom(int|string|null $value, string $key): static
    {
        $enum = static::tryFrom($value);
        throw_if(is_null($enum), ValidationException::withMessages([$key => __('validation.in', ['attribute' => $key])]));

        return $enum;
    }

    public function label(): string
    {
        return __('enums.'.self::class.'.'.$this->name);
    }

    public static function count(): int
    {
        return count(static::cases());
    }

    public static function parse(mixed $value): static
    {
        return ($value instanceof static) ? $value : static::from($value);
    }
}
