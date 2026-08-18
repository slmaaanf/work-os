<?php

namespace Database\Factories;

use App\Enums\DailyPlanActivityStatus;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyPlanActivityFactory extends Factory
{
    protected $model = DailyPlanActivity::class;

    public function definition(): array
    {
        return [
            'daily_plan_id' => DailyPlan::factory(),
            'activity_id' => Activity::factory(),
            'planned_mins' => 30,
            'status' => DailyPlanActivityStatus::PLANNED,
            'is_carried_over' => false,
            'carried_from_id' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => DailyPlanActivityStatus::IN_PROGRESS,
        ]);
    }

    public function doneToday(): static
    {
        return $this->state(fn () => [
            'status' => DailyPlanActivityStatus::DONE_TODAY,
        ]);
    }

    public function carriedOver(): static
    {
        return $this->state(fn () => [
            'is_carried_over' => true,
        ]);
    }
}