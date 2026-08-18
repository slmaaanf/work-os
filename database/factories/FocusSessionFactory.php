<?php

namespace Database\Factories;

use App\Enums\FocusSessionStatus;
use App\Models\DailyPlanActivity;
use App\Models\FocusSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class FocusSessionFactory extends Factory
{
    protected $model = FocusSession::class;

    public function definition(): array
    {
        return [
            'daily_plan_activity_id' => DailyPlanActivity::factory(),
            'status' => FocusSessionStatus::ACTIVE,
            'started_at' => now(),
            'paused_at' => null,
            'accumulated_pause_seconds' => 0,
            'finished_at' => null,
            'actual_duration_seconds' => null,
            'result' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(function () {
            $startedAt = now()->subMinutes(30);

            return [
                'status' => FocusSessionStatus::COMPLETED,
                'started_at' => $startedAt,
                'finished_at' => now(),
                'actual_duration_seconds' => 1800,
            ];
        });
    }

    public function abandoned(): static
    {
        return $this->state(function () {
            $startedAt = now()->subMinutes(30);

            return [
                'status' => FocusSessionStatus::ABANDONED,
                'started_at' => $startedAt,
                'finished_at' => now(),
                'actual_duration_seconds' => 1800,
            ];
        });
    }

    public function paused(): static
    {
        return $this->state(function () {
            return [
                'paused_at' => now()->subMinutes(5),
            ];
        });
    }
}