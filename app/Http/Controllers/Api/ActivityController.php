<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Services\ActivityService;

class ActivityController extends Controller
{
    public function __construct(private ActivityService $activityService) {}

    public function store(StoreActivityRequest $request)
    {
        $activity = $this->activityService->create(
            $request->user(), 
            $request->validated()
        );

        return new ActivityResource($activity);
    }
}