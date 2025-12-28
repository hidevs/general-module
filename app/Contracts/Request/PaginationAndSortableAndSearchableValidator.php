<?php

namespace Modules\General\Contracts\Request;

trait PaginationAndSortableAndSearchableValidator
{
    public function filters(): array
    {
        return $this->query(self::filtersKey(), []);
    }

    public function page(): int
    {
        return intval($this->query(self::pageKey(), 1));
    }

    public function perPage(): int
    {
        return intval($this->query(self::perPageKey(), 20));
    }

    public function orderBy(): string
    {
        $value = $this->query(self::orderByKey(), $this->getDefaultOrderColumn());

        return empty($value) ? $this->getDefaultOrderColumn() : $value;
    }

    public function sortedBy(): string
    {
        $value = strtoupper($this->query(self::sortedByKey(), 'desc'));

        return empty($value) ? 'DESC' : $value;
    }

    public function search(): ?string
    {
        return $this->query(self::searchKey());
    }

    public function searchFields(): ?string
    {
        return $this->query(self::searchFieldsKey());
    }

    public function searchJoin(): ?string
    {
        return $this->query(self::searchJoinKey());
    }

    public static function filtersKey(): string
    {
        return config('repository.filter.filters_key', 'filters');
    }

    public static function pageKey(): string
    {
        return config('repository.pagination.page_key', 'page');
    }

    public static function perPageKey(): string
    {
        return config('repository.pagination.per_page_key', 'perPage');
    }

    public static function orderByKey(): string
    {
        return config('repository.criteria.params.orderBy', 'orderBy');
    }

    public static function sortedByKey(): string
    {
        return config('repository.criteria.params.sortedBy', 'sortedBy');
    }

    public static function searchKey(): string
    {
        return config('repository.criteria.params.search', 'search');
    }

    public static function searchFieldsKey(): string
    {
        return config('repository.criteria.params.searchFields', 'searchFields');
    }

    public static function searchJoinKey(): string
    {
        return config('repository.criteria.params.searchJoin', 'searchJoin');
    }

    public function maxPerPage(): int
    {
        return 20;
    }

    public function unlimitedPerPage(): bool
    {
        return false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            self::pageKey() => $this->page(),
            self::perPageKey() => $this->perPage(),
            self::orderByKey() => $this->orderBy(),
            self::sortedByKey() => $this->sortedBy(),
            self::searchKey() => $this->search(),
            self::searchFieldsKey() => $this->searchFields(),
            self::searchJoinKey() => $this->searchJoin(),
        ]);

        if ($this->removeUnSafeQueryParams()) {
            collect($this->query())->each(function ($value, $key) {
                if (! in_array($key, array_keys($this->rules()))) {
                    $this->query->remove($key);
                }
            });
        }
    }

    public function removeUnSafeQueryParams(): bool
    {
        return true;
    }

    public function getDefaultOrderColumn(): string
    {
        return config('repository.pagination.default.order_by_column', 'id');
    }

    public function getOrderColumns(): array
    {
        return config('repository.pagination.default.order_by_columns', ['id', 'created_at']);
    }

    public function addRules(): array
    {
        return [];
    }

    public function rules(): array
    {
        return array_merge($this->defaultRules(), $this->addRules());
    }

    private function defaultRules(): array
    {
        return [
            self::filtersKey() => ['nullable', 'array'],
            self::pageKey() => ['required', 'numeric', 'min:1'],
            self::perPageKey() => ['required', 'numeric', $this->unlimitedPerPage() ? 'min:-1' : 'min:1', 'max:'.$this->maxPerPage()],
            self::orderByKey() => ['required', 'in:'.implode(',', array_unique(array_merge($this->getOrderColumns(), [$this->getDefaultOrderColumn()])))],
            self::sortedByKey() => ['required_with:'.self::orderByKey(), 'in:asc,desc,ASC,DESC'],
            self::searchKey() => ['nullable', 'string', 'min:2', 'max:255'],
            self::searchFieldsKey() => ['nullable', 'string'],
            self::searchJoinKey() => ['nullable', 'string', 'in:and,or,AND,OR'],
        ];
    }
}
