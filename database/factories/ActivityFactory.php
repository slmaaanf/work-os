<?php

namespace Database\Factories;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => null,
            'title' => fake()->sentence(4),
            'category' => fake()->randomElement(ActivityCategory::cases()),
            'status' => ActivityStatus::PLANNED,
            'completed_at' => null,
        ];
    }

    public function withProject(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'project_id' => Project::factory()->create([
                    'user_id' => $attributes['user_id'],
                ])->id,
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => ActivityStatus::IN_PROGRESS,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ActivityStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }
}