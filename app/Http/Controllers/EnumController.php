<?php

namespace Modules\General\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\General\Contracts\Controller\BaseController;
use Modules\General\Http\Requests\Enum\EnumShowRequest;
use Modules\General\Services\Enum\EnumService;

class EnumController extends BaseController
{
    public function __construct(private readonly EnumService $enumService) {}

    public function index(Request $request)
    {
        $enums = $this->enumService->all()->toArray();

        return JsonResource::collection($enums);
    }

    public function show(EnumShowRequest $request)
    {
        $enum = $this->enumService->find($request->route('module'), $request->route('name'));
        abort_if(empty($enum), 400, 'Enum class not found.');

        return new JsonResource($enum->toArray());
    }

    public function cases(EnumShowRequest $request)
    {
        $enum = $this->enumService->find($request->route('module'), $request->route('name'));
        abort_if(empty($enum), 400, 'Enum class not found.');

        return JsonResource::collection($enum->toArray()['cases']);
    }
}
