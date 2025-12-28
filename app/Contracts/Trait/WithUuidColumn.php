<?php

namespace Modules\General\Contracts\Trait;

use Illuminate\Support\Str;

trait WithUuidColumn
{
    protected static function bootWithUuidColumn(): void
    {
        static::creating(function (self $model) {
            if (! $model->getUuidKey()) {
                $model->{$model->getUuidKeyName()} = Str::orderedUuid()->toString();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return $this->getUuidKeyName();
    }

    public function getUuidKeyName(): string
    {
        return 'uuid';
    }

    public function getUuidKey(): ?string
    {
        return $this->{$this->getUuidKeyName()};
    }
}
