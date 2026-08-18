<?php

namespace App\Http\Resources;

use App\Enums\FocusSessionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FocusSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();
        $elapsedSeconds = null;

        if ($this->status === FocusSessionStatus::ACTIVE) {
            $endTime = $this->paused_at ?? $now;

            $elapsedSeconds = max(
                0,
                $this->started_at->diffInSeconds($endTime)
                    - $this->accumulated_pause_seconds
            );
        } elseif ($this->actual_duration_seconds !== null) {
            $elapsedSeconds = $this->actual_duration_seconds;
        }

        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'started_at' => $this->started_at?->toISOString(),
            'paused_at' => $this->paused_at?->toISOString(),
            'accumulated_pause_seconds' => $this->accumulated_pause_seconds,
            'elapsed_seconds' => $elapsedSeconds,
            'actual_duration_seconds' => $this->actual_duration_seconds,
            'result' => $this->result,
            'daily_plan_activity' => new DailyPlanActivityResource($this->whenLoaded('dailyPlanActivity')),
        ];
    }
}