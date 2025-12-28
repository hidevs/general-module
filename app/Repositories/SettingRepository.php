<?php

namespace Modules\General\Repositories;

use Modules\General\Contracts\Repository\BaseApiRepository;
use Rawilk\Settings\Facades\Settings;
use Rawilk\Settings\Models\Setting;

class SettingRepository extends BaseApiRepository
{
    protected $fieldSearchable = [
        'key' => 'like',
    ];

    public function model(): string
    {
        return Setting::class;
    }

    public function all($columns = ['*'])
    {
        return Settings::all();
    }
}
