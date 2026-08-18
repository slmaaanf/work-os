<?php

namespace App\Services;

use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Exceptions\ConflictException;
use App\Exceptions\UnauthorizedActionException;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\DailyPlanActivity;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DailyPlanService
{
    /**
     * Get existing daily plan for a user and date,
     * or create a new one if it does not exist.
     */
    public function getOrCreateForDate(
        User $user,
        string $date
    ): DailyPlan {
        try {
            return $user->dailyPlans()->firstOrCreate([
                'date' => $date,
            ]);
        } catch (QueryException $e) {
            /*
             * The database has a unique constraint:
             *
             * unique(['user_id', 'date'])
             *
             * If two requests try to create the same daily plan
             * concurrently, MySQL may throw a duplicate key error.
             */
            if ($e->getCode() === '23000') {
                $dailyPlan = $user->dailyPlans()
                    ->whereDate('date', $date)
                    ->first();

                if ($dailyPlan) {
                    return $dailyPlan;
                }

                throw new ConflictException(
                    'Daily plan already exists for this date.'
                );
            }

            throw $e;
        }
    }

    /**
     * Add an activity to a user's daily plan.
     */
    public function addActivity(
        User $user,
        string $date,
        Activity $activity,
        ?int $plannedMins = null
    ): DailyPlanActivity {
        /*
         * 1. Authorization
         *
         * A user can only add their own activity.
         */
        if ($activity->user_id !== $user->id) {
            throw new UnauthorizedActionException(
                'You do not own this activity.'
            );
        }

        /*
         * 2. Business rule
         *
         * Completed activities cannot be planned again.
         */
        if ($activity->status === ActivityStatus::COMPLETED) {
            throw new ConflictException(
                'Completed activities cannot be added to a daily plan.'
            );
        }

        /*
         * 3. Validate planned minutes
         */
        if ($plannedMins !== null && $plannedMins <= 0) {
            throw new ConflictException(
                'Planned minutes must be greater than zero.'
            );
        }

        /*
         * 4. Create DailyPlanActivity inside transaction.
         */
        try {
            return DB::transaction(function () use (
                $user,
                $date,
                $activity,
                $plannedMins
            ) {
                $dailyPlan = $this->getOrCreateForDate(
                    $user,
                    $date
                );

                /*
                 * Application-level duplicate check.
                 *
                 * Database also protects this with:
                 *
                 * unique(['daily_plan_id', 'activity_id'])
                 */
                $exists = $dailyPlan->activities()
                    ->where('activity_id', $activity->id)
                    ->exists();

                if ($exists) {
                    throw new ConflictException(
                        'Activity already exists in this daily plan.'
                    );
                }

                return $dailyPlan->activities()->create([
                    'activity_id' => $activity->id,
                    'planned_mins' => $plannedMins,
                    'status' => DailyPlanActivityStatus::PLANNED,
                    'is_carried_over' => false,
                    'carried_from_id' => null,
                ]);
            });
        } catch (ConflictException $e) {
            /*
             * Do not convert our own domain exception.
             * Let it propagate to the caller/test.
             */
            throw $e;
        } catch (QueryException $e) {
            /*
             * Database-level protection against concurrent duplicate
             * inserts.
             *
             * The relevant database constraint is:
             *
             * unique(['daily_plan_id', 'activity_id'])
             */
            if (
                $e->getCode() === '23000'
                && str_contains(
                    $e->getMessage(),
                    'Duplicate entry'
                )
            ) {
                throw new ConflictException(
                    'Activity already exists in this daily plan.'
                );
            }

            throw $e;
        }
    }
}