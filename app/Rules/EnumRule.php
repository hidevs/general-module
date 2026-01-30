<?php

namespace Modules\General\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EnumRule implements ValidationRule
{
    public function __construct(private readonly string $type) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $enum = $this->type::tryFrom($value);
        if (empty($enum)) {
            $fail(__('validation.in', ['attribute' => $attribute]));
        }
    }
}
