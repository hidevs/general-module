<?php

namespace Modules\General\Http\Resources;

use Illuminate\Http\Request;
use Modules\General\Contracts\Resource\BaseResource;

class EnumResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            '_type' => class_basename($this->resource),
            'name' => $this->resource->name,
            'label' => $this->resource->label(),
            'translate' => $this->resource->translate($this->additional['lang_path'] ?? ''),
            'value' => $this->resource->value,
        ];
    }
}
