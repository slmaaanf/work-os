<?php

namespace App\Services;

use App\Models\DailyPlanActivity;
use App\Models\FocusSession;
use App\Models\User;
use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Enums\FocusSessionStatus;
use App\Enums\FocusSessionDecision;
use App\Exceptions\ConflictException;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\InvalidStateException;
use Illuminate\Support\Facades\DB;

class FocusSessionService
{
    public function start(DailyPlanActivity $dpa, User $user): FocusSession
    {
        if ($dpa->dailyPlan->user_id !== $user->id) {
            throw new UnauthorizedActionException('You do not own this activity.');
        }

        return DB::transaction(function () use ($dpa, $user) {
            // 1. Serialization: Lock the user row to prevent concurrent START requests
            User::query()->whereKey($user->id)->lockForUpdate()->first();

            // 2. Business Validations
            if ($dpa->status === DailyPlanActivityStatus::DONE_TODAY) {
                throw new ConflictException('Cannot start a session for a daily activity that is already done today.');
            }
            if ($dpa->activity->status === ActivityStatus::COMPLETED) {
                throw new ConflictException('Cannot start a session for a completed activity.');
            }

            // 3. Ensure no other ACTIVE sessions exist for this user
            $hasActiveSession = FocusSession::whereHas('dailyPlanActivity.dailyPlan', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', FocusSessionStatus::ACTIVE)->exists();

            if ($hasActiveSession) {
                throw new ConflictException('User already has an active focus session.');
            }

            // 4. State Transitions
            $dpa->update(['status' => DailyPlanActivityStatus::IN_PROGRESS]);
            
            if ($dpa->activity->status === ActivityStatus::PLANNED) {
                $dpa->activity->update(['status' => ActivityStatus::IN_PROGRESS]);
            }

            // 5. Execution
            return $dpa->focusSessions()->create([
                'status' => FocusSessionStatus::ACTIVE,
                'started_at' => now(),
            ]);
        });
    }

    public function pause(int $sessionId, User $user): void
    {
        DB::transaction(function () use ($sessionId, $user) {
            $session = FocusSession::query()->whereKey($sessionId)->lockForUpdate()->firstOrFail();

            if ($session->dailyPlanActivity->dailyPlan->user_id !== $user->id) {
                throw new UnauthorizedActionException();
            }
            if ($session->status !== FocusSessionStatus::ACTIVE) {
                throw new InvalidStateException('Session is not active.');
            }

            if (!$session->paused_at) {
                $session->update(['paused_at' => now()]);
            }
        });
    }

    public function resume(int $sessionId, User $user): void
    {
        DB::transaction(function () use ($sessionId, $user) {
            $session = FocusSession::query()->whereKey($sessionId)->lockForUpdate()->firstOrFail();

            if ($session->dailyPlanActivity->dailyPlan->user_id !== $user->id) {
                throw new UnauthorizedActionException();
            }
            if ($session->status !== FocusSessionStatus::ACTIVE) {
                throw new InvalidStateException('Session is not active.');
            }
            if (!$session->paused_at) {
                throw new InvalidStateException('Session is not paused.');
            }

            $pauseDuration = $session->paused_at->diffInSeconds(now());

            $session->update([
                'accumulated_pause_seconds' => $session->accumulated_pause_seconds + $pauseDuration,
                'paused_at' => null,
            ]);
        });
    }

    public function finish(int $sessionId, User $user, FocusSessionDecision $decision, ?string $result = null): void
    {
        DB::transaction(function () use ($sessionId, $user, $decision, $result) {
            $session = FocusSession::query()->whereKey($sessionId)->lockForUpdate()->firstOrFail();

            if ($session->dailyPlanActivity->dailyPlan->user_id !== $user->id) {
                throw new UnauthorizedActionException();
            }
            if ($session->status !== FocusSessionStatus::ACTIVE) {
                throw new InvalidStateException('Session is not active.');
            }

            $now = now();

            // 1. Pause Normalization
            $accumulatedPauses = $session->accumulated_pause_seconds;
            if ($session->paused_at) {
                $accumulatedPauses += $session->paused_at->diffInSeconds($now);
            }

            // 2. Lock Final State
            $session->update([
                'finished_at' => $now,
                'paused_at' => null, // Normalize
                'accumulated_pause_seconds' => $accumulatedPauses, // Normalize
                'actual_duration_seconds' => max(0, $session->started_at->diffInSeconds($now) - $accumulatedPauses),
                'result' => $result,
                'status' => FocusSessionStatus::COMPLETED,
            ]);

            // 3. DPA & Activity State Machine Transitions
            $dpa = $session->dailyPlanActivity;
            $activity = $dpa->activity;

            if ($decision === FocusSessionDecision::DONE_TODAY || $decision === FocusSessionDecision::COMPLETED) {
                $dpa->update(['status' => DailyPlanActivityStatus::DONE_TODAY]);
            }

            if ($decision === FocusSessionDecision::COMPLETED) {
                $activity->update([
                    'status' => ActivityStatus::COMPLETED,
                    'completed_at' => $now,
                ]);
            }
        });
    }

    public function abandon(int $sessionId, User $user): void
    {
        DB::transaction(function () use ($sessionId, $user) {
            $session = FocusSession::query()->whereKey($sessionId)->lockForUpdate()->firstOrFail();

            if ($session->dailyPlanActivity->dailyPlan->user_id !== $user->id) {
                throw new UnauthorizedActionException();
            }
            if ($session->status !== FocusSessionStatus::ACTIVE) {
                throw new InvalidStateException('Only active sessions can be abandoned.');
            }

            $now = now();

            // Normalize time just like finish, but without DPA transitions
            $accumulatedPauses = $session->accumulated_pause_seconds;
            if ($session->paused_at) {
                $accumulatedPauses += $session->paused_at->diffInSeconds($now);
            }

            $session->update([
                'status' => FocusSessionStatus::ABANDONED,
                'finished_at' => $now,
                'paused_at' => null,
                'accumulated_pause_seconds' => $accumulatedPauses,
                'actual_duration_seconds' => max(0, $session->started_at->diffInSeconds($now) - $accumulatedPauses),
            ]);
        });
    }
}