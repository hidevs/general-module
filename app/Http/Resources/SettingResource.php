<?php

namespace Modules\General\Http\Resources;

use Illuminate\Http\Request;
use Modules\General\Contracts\Resource\BaseResource;
use Rawilk\Settings\Facades\Settings;

class SettingResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            '_type' => 'Setting',
            'key' => $this->resource,
            'value' => Settings::get($this->resource),
        ];
    }
}
