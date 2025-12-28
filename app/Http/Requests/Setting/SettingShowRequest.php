<?php

namespace Modules\General\Http\Requests\Setting;

use Illuminate\Validation\Rule;
use Modules\General\Contracts\Request\BaseRequest;
use Modules\General\Models\Setting;

class SettingShowRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'setting' => $this->route('setting'),
        ]);
    }

    public function rules(): array
    {
        return [
            'setting' => ['required', 'string', Rule::exists((new Setting)->getTable(), 'key')],
        ];
    }
}
