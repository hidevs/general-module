<?php

namespace Modules\General\Http\Requests\Setting;

use Modules\General\Contracts\Request\BaseRequest;
use Modules\General\Contracts\Request\PaginationAndSortableAndSearchableValidator;

class SettingIndexRequest extends BaseRequest
{
    use PaginationAndSortableAndSearchableValidator;

    public function getOrderColumns(): array
    {
        return ['id', 'key'];
    }

    public function getDefaultOrderColumn(): string
    {
        return 'key';
    }
}
