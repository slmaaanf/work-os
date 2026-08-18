<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'status' => ProjectStatus::ACTIVE,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => ProjectStatus::ARCHIVED,
        ]);
    }
}