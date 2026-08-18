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

function createActiveSessionFor(User $user)
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

it('can pause an active session', function () {
    $user = User::factory()->create();

    $session = createActiveSessionFor($user);

    app(FocusSessionService::class)->pause(
        $session->id,
        $user
    );

    $session->refresh();

    expect($session->status)->toBe(FocusSessionStatus::ACTIVE)
        ->and($session->paused_at)->not->toBeNull();
});

it('does not overwrite an existing paused_at when pause is called twice', function () {
    $user = User::factory()->create();

    $session = createActiveSessionFor($user);

    $this->travelTo('2026-08-18 10:10:00');

    $service = app(FocusSessionService::class);

    $service->pause($session->id, $user);

    $session->refresh();

    $firstPausedAt = $session->paused_at;

    $this->travelTo('2026-08-18 10:20:00');

    $service->pause($session->id, $user);

    $session->refresh();

    expect($session->paused_at->equalTo($firstPausedAt))
        ->toBeTrue();

    $this->travelBack();
});

it('can resume a paused session and accumulates pause duration', function () {
    $user = User::factory()->create();

    $this->travelTo('2026-08-18 10:00:00');

    $session = createActiveSessionFor($user);

    $service = app(FocusSessionService::class);

    $this->travelTo('2026-08-18 10:20:00');

    $service->pause($session->id, $user);

    $this->travelTo('2026-08-18 10:30:00');

    $service->resume($session->id, $user);

    $session->refresh();

    expect($session->paused_at)->toBeNull()
        ->and($session->accumulated_pause_seconds)->toBe(600);

    $this->travelBack();
});

it('rejects resume when session is not paused', function () {
    $user = User::factory()->create();

    $session = createActiveSessionFor($user);

    expect(fn () => app(FocusSessionService::class)->resume(
        $session->id,
        $user
    ))->toThrow(InvalidStateException::class);
});

it('rejects pause for another users session', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $session = createActiveSessionFor($owner);

    expect(fn () => app(FocusSessionService::class)->pause(
        $session->id,
        $attacker
    ))->toThrow(UnauthorizedActionException::class);
});

it('rejects resume for another users session', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $session = createActiveSessionFor($owner);

    app(FocusSessionService::class)->pause(
        $session->id,
        $owner
    );

    expect(fn () => app(FocusSessionService::class)->resume(
        $session->id,
        $attacker
    ))->toThrow(UnauthorizedActionException::class);
});