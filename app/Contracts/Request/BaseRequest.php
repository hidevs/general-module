<?php

namespace Modules\General\Contracts\Request;

use Illuminate\Foundation\Http\FormRequest;
use Modules\General\Contracts\DTO\BaseInput;

abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function asInput(): BaseInput
    {
        throw new \Exception('Class '.static::class.' should implement asInput() method.');
    }

    public function mergeRouteParams(): void
    {
        $this->merge($this->route()->parameters());
    }
}
