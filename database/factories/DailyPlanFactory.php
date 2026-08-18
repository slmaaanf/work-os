<?php

namespace Database\Factories;

use App\Models\DailyPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyPlanFactory extends Factory
{
    protected $model = DailyPlan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => fake()->date(),
            'daily_win' => null,
            'oops_moment' => null,
            'lesson_learned' => null,
        ];
    }
}