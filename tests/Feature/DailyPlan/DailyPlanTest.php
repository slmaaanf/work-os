<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Exceptions\ConflictException;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use App\Services\DailyPlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

uses(RefreshDatabase::class);

it('creates one daily plan for a user and date', function () {
    $user = User::factory()->create();

    $service = app(DailyPlanService::class);

    $first = $service->getOrCreateForDate(
        $user,
        '2026-08-18'
    );

    $second = $service->getOrCreateForDate(
        $user,
        '2026-08-18'
    );

    expect($first->id)->toBe($second->id);

    expect(
        DailyPlan::where('user_id', $user->id)
            ->whereDate('date', '2026-08-18')
            ->count()
    )->toBe(1);
});

it('allows the same user to have daily plans on different dates', function () {
    $user = User::factory()->create();

    $service = app(DailyPlanService::class);

    $first = $service->getOrCreateForDate(
        $user,
        '2026-08-18'
    );

    $second = $service->getOrCreateForDate(
        $user,
        '2026-08-19'
    );

    expect($first->id)->not->toBe($second->id);

    expect(
        DailyPlan::where('user_id', $user->id)->count()
    )->toBe(2);
});

it('allows adding an activity to a daily plan', function () {
    $user = User::factory()->create();

    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'status' => ActivityStatus::PLANNED,
        'category' => ActivityCategory::WORK,
    ]);

    $service = app(DailyPlanService::class);

    $dpa = $service->addActivity(
        $user,
        '2026-08-18',
        $activity,
        30
    );

    expect($dpa)
        ->toBeInstanceOf(DailyPlanActivity::class)
        ->and($dpa->activity_id)->toBe($activity->id)
        ->and($dpa->planned_mins)->toBe(30)
        ->and($dpa->status)->toBe(DailyPlanActivityStatus::PLANNED);
});

it('rejects adding a completed activity to a daily plan', function () {
    $user = User::factory()->create();

    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'status' => ActivityStatus::COMPLETED,
        'category' => ActivityCategory::WORK,
    ]);

    expect(fn () => app(DailyPlanService::class)->addActivity(
        $user,
        '2026-08-18',
        $activity,
        30
    ))->toThrow(ConflictException::class);
});

it('rejects adding the same activity twice to the same daily plan', function () {
    $user = User::factory()->create();

    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'status' => ActivityStatus::PLANNED,
        'category' => ActivityCategory::WORK,
    ]);

    $service = app(DailyPlanService::class);

    $service->addActivity(
        $user,
        '2026-08-18',
        $activity,
        30
    );

    expect(fn () => $service->addActivity(
        $user,
        '2026-08-18',
        $activity,
        45
    ))->toThrow(ConflictException::class);

    expect(
        DailyPlanActivity::where('activity_id', $activity->id)->count()
    )->toBe(1);
});

it('rejects adding another users activity', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $activity = Activity::factory()->create([
        'user_id' => $otherUser->id,
        'status' => ActivityStatus::PLANNED,
        'category' => ActivityCategory::WORK,
    ]);

    expect(fn () => app(DailyPlanService::class)->addActivity(
        $user,
        '2026-08-18',
        $activity,
        30
    ))->toThrow(\App\Exceptions\UnauthorizedActionException::class);
});