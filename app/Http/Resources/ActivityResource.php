<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category->value,
            'status' => $this->status->value,
            'project' => $this->project_id ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ] : null,
        ];
    }
}