<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyPlanActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'planned_mins' => $this->planned_mins,
            'status' => $this->status->value,
            'is_carried_over' => $this->is_carried_over,
            'activity' => new ActivityResource($this->whenLoaded('activity')),
            'focus_sessions' => FocusSessionResource::collection($this->whenLoaded('focusSessions')),
        ];
    }
}