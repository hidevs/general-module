<?php

namespace Modules\General\Contracts\Request;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait DynamicModelRequest
{
    public function validModels(): array
    {
        return array_keys(get_all_models());
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function addRules(): array
    {
        return [];
    }

    public function rules(): array
    {
        return array_merge($this->defaultRules(), $this->addRules());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'model_id' => $this->route('model_id'),
            'model_type' => $this->route('model_type'),
        ]);
    }

    private function modelType(): Model
    {
        $modelClass = get_all_models()[$this->route('model_type')];
        throw_if(is_null($modelClass), ValidationException::withMessages(['model_type' => __('validation.in', ['attribute' => 'model_type'])]));

        return resolve($modelClass);
    }

    public function model(): Model
    {
        return $this->modelType()->findOrFail($this->route('model_id'));
    }

    public function defaultRules(): array
    {
        return [
            'model_id' => ['required', $this->modelType()->getKeyType()],
            'model_type' => ['required', Rule::in($this->validModels())],
        ];
    }
}
