<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Exceptions\ConflictException;
use App\Exceptions\UnauthorizedActionException;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use App\Services\CarryOverService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCarryOverSource(
    User $user,
    string $date = '2026-08-18'
): DailyPlanActivity {
    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'category' => ActivityCategory::WORK,
        'status' => ActivityStatus::IN_PROGRESS,
    ]);

    $dailyPlan = DailyPlan::factory()->create([
        'user_id' => $user->id,
        'date' => $date,
    ]);

    return DailyPlanActivity::factory()->create([
        'daily_plan_id' => $dailyPlan->id,
        'activity_id' => $activity->id,
        'status' => DailyPlanActivityStatus::IN_PROGRESS,
    ]);
}

it('carries an activity to a future date', function () {
    $user = User::factory()->create();

    $source = createCarryOverSource($user);

    $newDpa = app(CarryOverService::class)->carryOver(
        $source,
        $user,
        '2026-08-19',
        45
    );

    expect($newDpa->activity_id)
        ->toBe($source->activity_id)
        ->and($newDpa->planned_mins)
        ->toBe(45)
        ->and($newDpa->is_carried_over)
        ->toBeTrue()
        ->and($newDpa->carried_from_id)
        ->toBe($source->id)
        ->and($newDpa->status)
        ->toBe(DailyPlanActivityStatus::PLANNED);
});

it('rejects carrying an activity to the same date', function () {
    $user = User::factory()->create();

    $source = createCarryOverSource($user);

    expect(fn () => app(CarryOverService::class)->carryOver(
        $source,
        $user,
        '2026-08-18'
    ))->toThrow(ConflictException::class);
});

it('rejects carrying an activity to a previous date', function () {
    $user = User::factory()->create();

    $source = createCarryOverSource($user);

    expect(fn () => app(CarryOverService::class)->carryOver(
        $source,
        $user,
        '2026-08-17'
    ))->toThrow(ConflictException::class);
});

it('rejects carrying an activity owned by another user', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $source = createCarryOverSource($owner);

    expect(fn () => app(CarryOverService::class)->carryOver(
        $source,
        $attacker,
        '2026-08-19'
    ))->toThrow(UnauthorizedActionException::class);
});

it('rejects carrying a completed activity', function () {
    $user = User::factory()->create();

    $source = createCarryOverSource($user);

    $source->activity->update([
        'status' => ActivityStatus::COMPLETED,
    ]);

    expect(fn () => app(CarryOverService::class)->carryOver(
        $source,
        $user,
        '2026-08-19'
    ))->toThrow(ConflictException::class);
});

it('rejects duplicate carry over to the same target date', function () {
    $user = User::factory()->create();

    $source = createCarryOverSource($user);

    $service = app(CarryOverService::class);

    $service->carryOver(
        $source,
        $user,
        '2026-08-19'
    );

    expect(fn () => $service->carryOver(
        $source,
        $user,
        '2026-08-19'
    ))->toThrow(ConflictException::class);

    expect(
        DailyPlanActivity::where('activity_id', $source->activity_id)->count()
    )->toBe(2);
});