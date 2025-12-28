<?php

namespace Modules\General\Services\Enum;

use Illuminate\Support\Collection;
use Modules\General\Contracts\Service\BaseService;
use Modules\General\Services\Enum\Data\EnumClassOutput;

class EnumService extends BaseService
{
    /**
     * @return Collection<EnumClassOutput>
     */
    public function all(): Collection
    {
        return collect(glob(base_path('Modules/*/app/Enums/*.php')))
            ->map(function ($path) {
                if (! preg_match('#/Modules/([^/]+)/app/Enums/(.+)\.php$#', $path, $matches)) {
                    return;
                }

                [$_, $module, $enumFile] = $matches;
                $class = "Modules\\{$module}\\Enums\\".str_replace('/', '\\', $enumFile);

                if (! class_exists($class) || ! enum_exists($class)) {
                    return;
                }

                return new EnumClassOutput($class, class_basename($class), $module);
            })->filter()->values();
    }

    public function find(string $module, string $name): ?EnumClassOutput
    {
        return $this->all()->where('module', $module)->where('name', $name)->first();
    }
}
