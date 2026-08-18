<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Enums\FocusSessionStatus;
use App\Exceptions\ConflictException;
use App\Exceptions\UnauthorizedActionException;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\FocusSession;
use App\Models\User;
use App\Services\FocusSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createStartableDpa(
    User $user,
    DailyPlan $dailyPlan
): DailyPlanActivity {
    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'category' => ActivityCategory::WORK,
        'status' => ActivityStatus::PLANNED,
    ]);

    return DailyPlanActivity::factory()->create([
        'daily_plan_id' => $dailyPlan->id,
        'activity_id' => $activity->id,
        'status' => DailyPlanActivityStatus::PLANNED,
    ]);
}

it('starts a focus session successfully', function () {
    $user = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $dpa = createStartableDpa($user, $dailyPlan);

    $session = app(FocusSessionService::class)->start(
        $dpa,
        $user
    );

    expect($session->status)->toBe(FocusSessionStatus::ACTIVE)
        ->and($session->started_at)->not->toBeNull();

    expect($dpa->refresh()->status)
        ->toBe(DailyPlanActivityStatus::IN_PROGRESS);

    expect($dpa->activity->refresh()->status)
        ->toBe(ActivityStatus::IN_PROGRESS);
});

it('does not change an already in progress activity back to planned', function () {
    $user = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $dpa = createStartableDpa($user, $dailyPlan);

    $dpa->activity->update([
        'status' => ActivityStatus::IN_PROGRESS,
    ]);

    $session = app(FocusSessionService::class)->start(
        $dpa,
        $user
    );

    expect($session->status)->toBe(FocusSessionStatus::ACTIVE)
        ->and($dpa->activity->refresh()->status)
        ->toBe(ActivityStatus::IN_PROGRESS);
});

it('rejects starting a done today activity', function () {
    $user = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $dpa = createStartableDpa($user, $dailyPlan);

    $dpa->update([
        'status' => DailyPlanActivityStatus::DONE_TODAY,
    ]);

    expect(fn () => app(FocusSessionService::class)->start(
        $dpa,
        $user
    ))->toThrow(ConflictException::class);
});

it('rejects starting a completed activity', function () {
    $user = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $dpa = createStartableDpa($user, $dailyPlan);

    $dpa->activity->update([
        'status' => ActivityStatus::COMPLETED,
    ]);

    expect(fn () => app(FocusSessionService::class)->start(
        $dpa,
        $user
    ))->toThrow(ConflictException::class);
});

it('rejects starting another users daily activity', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $owner->id,
        'date' => '2026-08-18',
    ]);

    $dpa = createStartableDpa($owner, $dailyPlan);

    expect(fn () => app(FocusSessionService::class)->start(
        $dpa,
        $attacker
    ))->toThrow(UnauthorizedActionException::class);
});

it('prevents a user from having two active sessions', function () {
    $user = User::factory()->create();

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $firstDpa = createStartableDpa($user, $dailyPlan);
    $secondDpa = createStartableDpa($user, $dailyPlan);

    $service = app(FocusSessionService::class);

    $service->start($firstDpa, $user);

    expect(fn () => $service->start($secondDpa, $user))
        ->toThrow(ConflictException::class);

    expect(
        FocusSession::where('status', FocusSessionStatus::ACTIVE)->count()
    )->toBe(1);
});