<?php

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Exceptions\UnauthorizedActionException;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a user to create an activity', function () {
    $user = User::factory()->create();

    $activity = app(ActivityService::class)->create($user, [
        'title' => 'Build Work OS API',
        'category' => ActivityCategory::WORK->value,
    ]);

    expect($activity)
        ->toBeInstanceOf(Activity::class)
        ->and($activity->user_id)->toBe($user->id)
        ->and($activity->title)->toBe('Build Work OS API')
        ->and($activity->category)->toBe(ActivityCategory::WORK)
        ->and($activity->status)->toBe(ActivityStatus::PLANNED);
});

it('allows creating an activity without a project', function () {
    $user = User::factory()->create();

    $activity = app(ActivityService::class)->create($user, [
        'title' => 'Learn Laravel',
        'category' => ActivityCategory::LEARN->value,
    ]);

    expect($activity->project_id)->toBeNull();
});

it('allows creating an activity with an owned project', function () {
    $user = User::factory()->create();

    $project = Project::factory()->create([
        'user_id' => $user->id,
    ]);

    $activity = app(ActivityService::class)->create($user, [
        'title' => 'Work OS Development',
        'category' => ActivityCategory::WORK->value,
        'project_id' => $project->id,
    ]);

    expect($activity->project_id)->toBe($project->id);
});

it('rejects creating an activity with another users project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $project = Project::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    expect(fn () => app(ActivityService::class)->create($user, [
        'title' => 'Malicious Activity',
        'category' => ActivityCategory::WORK->value,
        'project_id' => $project->id,
    ]))->toThrow(UnauthorizedActionException::class);
});

it('defaults newly created activities to planned status', function () {
    $user = User::factory()->create();

    $activity = app(ActivityService::class)->create($user, [
        'title' => 'New Activity',
        'category' => ActivityCategory::LIFE->value,
    ]);

    expect($activity->status)->toBe(ActivityStatus::PLANNED);
});