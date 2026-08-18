<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Enums\FocusSessionStatus;
use App\Exceptions\InvalidStateException;
use App\Exceptions\UnauthorizedActionException;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use App\Services\FocusSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAbandonableSession(User $user)
{
    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'category' => ActivityCategory::WORK,
        'status' => ActivityStatus::PLANNED,
    ]);

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => '2026-08-18',
    ]);

    $dpa = DailyPlanActivity::factory()->create([
        'daily_plan_id' => $dailyPlan->id,
        'activity_id' => $activity->id,
        'status' => DailyPlanActivityStatus::PLANNED,
    ]);

    return app(FocusSessionService::class)->start($dpa, $user);
}

it('can abandon an active session', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createAbandonableSession($user);

    $this->travelTo('2026-08-18 10:20:00');

    app(FocusSessionService::class)->abandon(
        $session->id,
        $user
    );

    $session->refresh();

    expect($session->status)
        ->toBe(FocusSessionStatus::ABANDONED)
        ->and($session->finished_at)
        ->not->toBeNull()
        ->and($session->actual_duration_seconds)
        ->toBe(1200)
        ->and($session->paused_at)
        ->toBeNull();

    $this->travelBack();
});

it('calculates duration correctly when abandoned while paused', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createAbandonableSession($user);

    $service = app(FocusSessionService::class);

    $this->travelTo('2026-08-18 10:20:00');

    $service->pause($session->id, $user);

    $this->travelTo('2026-08-18 10:30:00');

    $service->abandon($session->id, $user);

    $session->refresh();

    expect($session->status)
        ->toBe(FocusSessionStatus::ABANDONED)
        ->and($session->accumulated_pause_seconds)
        ->toBe(600)
        ->and($session->actual_duration_seconds)
        ->toBe(1200)
        ->and($session->paused_at)
        ->toBeNull();

    $this->travelBack();
});

it('does not change activity state when a session is abandoned', function () {
    $user = User::factory()->create();

    $session = createAbandonableSession($user);

    app(FocusSessionService::class)->abandon(
        $session->id,
        $user
    );

    $activity = $session->dailyPlanActivity->activity->refresh();
    $dpa = $session->dailyPlanActivity->refresh();

    expect($activity->status)
        ->toBe(ActivityStatus::IN_PROGRESS)
        ->and($dpa->status)
        ->toBe(DailyPlanActivityStatus::IN_PROGRESS);
});

it('rejects abandoning a non active session', function () {
    $user = User::factory()->create();

    $session = createAbandonableSession($user);

    app(FocusSessionService::class)->abandon(
        $session->id,
        $user
    );

    expect(fn () => app(FocusSessionService::class)->abandon(
        $session->id,
        $user
    ))->toThrow(InvalidStateException::class);
});

it('rejects abandoning another users session', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $session = createAbandonableSession($owner);

    expect(fn () => app(FocusSessionService::class)->abandon(
        $session->id,
        $attacker
    ))->toThrow(UnauthorizedActionException::class);
});