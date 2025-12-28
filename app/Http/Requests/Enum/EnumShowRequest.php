<?php

namespace Modules\General\Http\Requests\Enum;

use Illuminate\Validation\Rule;
use Modules\General\Contracts\Request\BaseRequest;

class EnumShowRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'module' => $this->route('module'),
            'name' => $this->route('name'),
        ]);
    }

    public function rules(): array
    {
        return [
            'module' => ['required', 'string', Rule::in(get_all_modules())],
            'name' => ['required', 'string'],
        ];
    }
}
