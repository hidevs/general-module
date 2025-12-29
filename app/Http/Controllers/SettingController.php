<?php

namespace Modules\General\Http\Controllers;

use Modules\General\Contracts\Controller\BaseController;
use Modules\General\Http\Requests\Setting\SettingIndexRequest;
use Modules\General\Http\Requests\Setting\SettingShowRequest;
use Modules\General\Http\Resources\SettingResource;
use Modules\General\Repositories\SettingRepository;

class SettingController extends BaseController
{
    public function __construct(private readonly SettingRepository $repository) {}

    public function index(SettingIndexRequest $request)
    {
        $settings = $this->repository->paginate(-1)
            ->sortBy($request->orderBy(), descending: $request->sortedBy() === 'DESC')
            ->pluck('key');

        return SettingResource::collection($settings);
    }

    public function show(SettingShowRequest $request): SettingResource
    {
        return new SettingResource($request->setting);
    }
}
