<?php

namespace Modules\General\Services\Enum\Data;

use Modules\General\Contracts\DTO\BaseOutput;

class EnumClassOutput extends BaseOutput
{
    public function __construct(
        private string $class,
        public string $name,
        public string $module,
    ) {}

    public function class(): string
    {
        return $this->class;
    }

    public function cases(): array
    {
        return $this->class::cases();
    }

    public function values(): array
    {
        return $this->class::values();
    }

    public function names(): array
    {
        return $this->class::names();
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'cases' => array_map(fn ($case) => $this->caseMap($case), $this->cases()),
        ];
    }

    public function caseMap($case): array
    {
        return [
            'name' => $case->name,
            'value' => $case->value,
            'label' => $case->label(),
            'translate' => $case->translate(strtolower($this->module).'::enums.cases'),
        ];
    }
}
