<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Enums\FocusSessionDecision;
use App\Enums\FocusSessionStatus;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use App\Services\FocusSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createFinishableSession(User $user)
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

it('calculates actual duration correctly when finished while paused', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    $service = app(FocusSessionService::class);

    // Running: 20 minutes
    $this->travelTo('2026-08-18 10:20:00');

    $service->pause($session->id, $user);

    // Paused: 10 minutes
    $this->travelTo('2026-08-18 10:30:00');

    $service->finish(
        $session->id,
        $user,
        FocusSessionDecision::DONE_TODAY,
        'Finished successfully'
    );

    $session->refresh();

    expect($session->status)
        ->toBe(FocusSessionStatus::COMPLETED)
        ->and($session->actual_duration_seconds)
        ->toBe(1200)
        ->and($session->accumulated_pause_seconds)
        ->toBe(600)
        ->and($session->paused_at)
        ->toBeNull()
        ->and($session->result)
        ->toBe('Finished successfully');

    $this->travelBack();
});

it('finishes a session without pause correctly', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    $this->travelTo('2026-08-18 10:25:00');

    app(FocusSessionService::class)->finish(
        $session->id,
        $user,
        FocusSessionDecision::DONE_TODAY
    );

    $session->refresh();

    expect($session->actual_duration_seconds)
        ->toBe(1500)
        ->and($session->accumulated_pause_seconds)
        ->toBe(0)
        ->and($session->status)
        ->toBe(FocusSessionStatus::COMPLETED);

    $this->travelBack();
});

it('changes daily plan activity to done today when decision is done today', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    app(FocusSessionService::class)->finish(
        $session->id,
        $user,
        FocusSessionDecision::DONE_TODAY
    );

    $session->refresh();

    expect($session->dailyPlanActivity->refresh()->status)
        ->toBe(DailyPlanActivityStatus::DONE_TODAY);

    expect($session->dailyPlanActivity->activity->refresh()->status)
        ->toBe(ActivityStatus::IN_PROGRESS);

    $this->travelBack();
});

it('completes the activity when decision is completed', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    $this->travelTo('2026-08-18 10:30:00');

    app(FocusSessionService::class)->finish(
        $session->id,
        $user,
        FocusSessionDecision::COMPLETED,
        'Everything completed.'
    );

    $session->refresh();

    $activity = $session->dailyPlanActivity->activity->refresh();
    $dpa = $session->dailyPlanActivity->refresh();

    expect($activity->status)
        ->toBe(ActivityStatus::COMPLETED)
        ->and($activity->completed_at)
        ->not->toBeNull()
        ->and($dpa->status)
        ->toBe(DailyPlanActivityStatus::DONE_TODAY);

    $this->travelBack();
});

it('keeps the activity in progress when decision is continue later', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    app(FocusSessionService::class)->finish(
        $session->id,
        $user,
        FocusSessionDecision::CONTINUE_LATER
    );

    $session->refresh();

    expect($session->dailyPlanActivity->refresh()->status)
        ->toBe(DailyPlanActivityStatus::IN_PROGRESS);

    expect($session->dailyPlanActivity->activity->refresh()->status)
        ->toBe(ActivityStatus::IN_PROGRESS);

    $this->travelBack();
});

it('normalizes pause state when finishing while paused', function () {
    $this->travelTo('2026-08-18 10:00:00');

    $user = User::factory()->create();

    $session = createFinishableSession($user);

    $this->travelTo('2026-08-18 10:15:00');

    app(FocusSessionService::class)->pause(
        $session->id,
        $user
    );

    $this->travelTo('2026-08-18 10:25:00');

    app(FocusSessionService::class)->finish(
        $session->id,
        $user,
        FocusSessionDecision::DONE_TODAY
    );

    $session->refresh();

    expect($session->paused_at)->toBeNull()
        ->and($session->accumulated_pause_seconds)->toBe(600)
        ->and($session->actual_duration_seconds)->toBe(900);

    $this->travelBack();
});