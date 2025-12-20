<?php

namespace Modules\General\Contracts\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Prettus\Repository\Eloquent\BaseRepository as PrettusBaseRepository;

abstract class BaseApiRepository extends PrettusBaseRepository
{
    protected array $fieldFilterable = [];

    public function filters(array $filters): static
    {
        $this->model->where(function () use ($filters) {
            $filters = array_filter($filters, fn ($value, $key) => in_array($key, $this->fieldFilterable), ARRAY_FILTER_USE_BOTH);
            foreach ($filters as $key => $term) {
                $method = Str::camel("filter_{$key}");
                if (! method_exists($this, $method)) {
                    throw ValidationException::withMessages([
                        "filters.{$key}" => __('validation.filter', ['attribute' => __($key)]),
                    ]);
                } else {
                    $this->{$method}($term);
                }
            }
        });

        return $this;
    }

    protected function applyAndReset(\Closure $callback): mixed
    {
        $this->withCriteria(! $this->skipCriteria);
        $this->applyScope();
        $result = $callback();
        $this->resetModel();

        return $this->parserResult($result);
    }

    public function disableSearching(): static
    {
        $this->fieldSearchable = [];

        return $this;
    }

    public function boot(): void
    {
        $this->pushCriteria($this->apiRequestCriteria());
    }

    public function apiRequestCriteria(): ApiRequestCriteria
    {
        return app(ApiRequestCriteria::class);
    }

    public function withCriteria(bool $apply = true): static
    {
        $this->{$apply ? 'pushCriteria' : 'popCriteria'}($this->apiRequestCriteria());

        return $this;
    }

    public function firstOrCreate(array $attributes = [], array $values = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->model->firstOrCreate($attributes, $values);
        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();

        return $this->parserResult($model);
    }

    public function findBy(string $value, string $column, bool $throw = true): ?Model
    {
        return $this->applyAndReset(fn () => $this->model->newQuery()->where($column, $value)->{$throw ? 'firstOrFail' : 'first'}());
    }

    public function findByKey(string $value, ?string $keyName = null, bool $throw = true): ?Model
    {
        return $this->findBy($value, $keyName ?? resolve($this->model())->getKeyName(), $throw);
    }

    public function findByRouteKey(string $key, ?string $routeKeyName = null, bool $throw = true): ?Model
    {
        return $this->findBy($key, $routeKeyName ?? resolve($this->model())->getRouteKeyName(), $throw);
    }

    public function exists(string $field, mixed $value, mixed $ignoreId = null, string $ignoreColumn = 'id'): bool
    {
        return $this->applyAndReset(function () use ($field, $value, $ignoreId, $ignoreColumn) {
            return is_null($ignoreId)
                ? $this->model->where($field, $value)->exists()
                : $this->model->where($field, $value)->whereNot($ignoreColumn, $ignoreId)->exists();
        });
    }

    public function updateBy(string $key, string $column, array $attributes): Model
    {
        $model = $this->findBy($key, $column);
        $model->update($attributes);

        return $model;
    }

    public function updateByRouteKey(string $key, array $attributes): Model
    {
        $model = $this->findByRouteKey($key);
        $model->update($attributes);

        return $model;
    }

    public function deleteBy(string $key, string $column): bool
    {
        return $this->findBy($key, $column)->delete();
    }

    public function deleteByRouteKey(string $key): bool
    {
        return $this->findByRouteKey($key)->delete();
    }
}
