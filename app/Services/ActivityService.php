<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Enums\ActivityStatus;
use App\Enums\ActivityCategory;

class ActivityService
{
   public function create(User $user, array $data): Activity
    {
    if (!empty($data['project_id'])) {
        $ownsProject = $user->projects()->whereKey($data['project_id'])->exists();
        if (!$ownsProject) {
            throw new \App\Exceptions\UnauthorizedActionException('You do not own this project.');
        }
    }

    return $user->activities()->create([
        'title' => $data['title'],
        'category' => \App\Enums\ActivityCategory::from($data['category']),
        'project_id' => $data['project_id'] ?? null,
        'status' => \App\Enums\ActivityStatus::PLANNED,
    ]);
    }
}