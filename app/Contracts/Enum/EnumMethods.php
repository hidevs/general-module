<?php

namespace Modules\General\Contracts\Enum;

use Illuminate\Validation\ValidationException;
use RuntimeException;

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

    public static function options(): array
    {
        return array_combine(static::values(), array_map(fn (self $case) => $case->label(), static::cases()));
    }

    public static function count(): int
    {
        return count(static::cases());
    }

    public static function parse(mixed $value): static
    {
        return ($value instanceof static) ? $value : static::from($value);
    }

    public function order(): int
    {
        return array_search($this, static::cases(), true);
    }

    /**
     * @return static[]
     */
    public static function ordered(): array
    {
        $cases = static::cases();
        usort($cases, fn (self $a, self $b) => $a->order() <=> $b->order());

        return $cases;
    }

    /**
     * @return static[]
     */
    public static function greaterThan(self $enum): array
    {
        return array_values(array_filter(
            static::ordered(),
            fn (self $case) => $case->order() < $enum->order(),
        ));
    }

    /**
     * @return static[]
     */
    public static function lessThan(self $enum): array
    {
        return array_values(array_filter(
            static::ordered(),
            fn (self $case) => $case->order() > $enum->order(),
        ));
    }

    /**
     * @return array<string, string>
     */
    public static function optionsGreaterThan(self $enum): array
    {
        $result = [];
        foreach (static::greaterThan($enum) as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    public static function optionsLessThan(self $enum): array
    {
        $result = [];
        foreach (static::lessThan($enum) as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }

    public static function default(): static
    {
        throw new RuntimeException(static::class.'::default() is not implemented.');
    }
}
