<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddDailyPlanActivityRequest;
use App\Http\Requests\Api\CarryOverRequest;
use App\Http\Resources\DailyPlanActivityResource;
use App\Services\DailyPlanService;
use App\Services\CarryOverService;
use App\Models\Activity;
use App\Models\DailyPlanActivity;

class DailyPlanActivityController extends Controller
{
    public function __construct(
        private DailyPlanService $dailyPlanService,
        private CarryOverService $carryOverService
    ) {}

    public function store(AddDailyPlanActivityRequest $request, string $date)
    {
    // Celah ditutup: Hanya mencari activity milik user yang sedang login
    $activity = $request->user()->activities()->findOrFail($request->validated('activity_id'));

    $dpa = $this->dailyPlanService->addActivity(
        $request->user(),
        $date,
        $activity,
        $request->validated('planned_mins')
    );

    $dpa->load(['activity.project', 'focusSessions']);
    return new DailyPlanActivityResource($dpa);
    }

    public function carryOver(CarryOverRequest $request, DailyPlanActivity $dailyPlanActivity)
    {
        $newDpa = $this->carryOverService->carryOver(
            $dailyPlanActivity,
            $request->user(),
            $request->validated('target_date'),
            $request->validated('planned_mins')
        );

        $newDpa->load(['activity.project', 'focusSessions']);

        return new DailyPlanActivityResource($newDpa);
    }
}