<?php

namespace Modules\General\Http\Resources;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Modules\General\Contracts\Resource\BaseResource;

class AgentResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'platform' => $this->agent()->platform(),
            'device' => $this->agent()->device(),
            'device_type' => $this->agent()->deviceType(),
            'browser' => $this->agent()->browser(),
            'is_desktop' => $this->agent()->isDesktop(),
        ];
    }

    public function agent(): Agent
    {
        return new Agent(@$this->resource['headers'], $this->resource['agent']);
    }
}
