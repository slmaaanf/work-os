<?php

namespace App\Services;

use App\Enums\ActivityStatus;
use App\Enums\DailyPlanActivityStatus;
use App\Exceptions\ConflictException;
use App\Exceptions\UnauthorizedActionException;
use App\Models\DailyPlanActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CarryOverService
{
    /**
     * Carry an activity from its current daily plan
     * to a future daily plan.
     */
    public function carryOver(
        DailyPlanActivity $sourceDpa,
        User $user,
        string $targetDate,
        ?int $plannedMins = null
    ): DailyPlanActivity {
        /*
         * 1. Load required relationships first.
         */
        $sourceDpa->load([
            'dailyPlan',
            'activity',
        ]);

        /*
         * 2. Authorization
         *
         * The user can only carry over activities
         * belonging to their own daily plan.
         */
        if ($sourceDpa->dailyPlan->user_id !== $user->id) {
            throw new UnauthorizedActionException(
                'You do not own this activity.'
            );
        }

        /*
         * 3. Get source and target dates.
         */
        $sourceDate = Carbon::parse(
            $sourceDpa->dailyPlan->date
        );

        $target = Carbon::parse($targetDate);

        /*
         * 4. Target date must be after source date.
         */
        if ($target->lessThanOrEqualTo($sourceDate)) {
            throw new ConflictException(
                'Target date must be in the future relative to the source.'
            );
        }

        /*
         * 5. Completed activities cannot be carried over.
         */
        if (
            $sourceDpa->activity->status
            === ActivityStatus::COMPLETED
        ) {
            throw new ConflictException(
                'Completed activity cannot be carried over.'
            );
        }

        /*
         * 6. If planned minutes are not explicitly provided,
         *    use the planned minutes from the source DPA.
         *
         * This is important because daily_plan_activities.planned_mins
         * is NOT nullable in the database.
         */
        $plannedMins ??= $sourceDpa->planned_mins;

        /*
         * 7. Validate planned minutes.
         */
        if ($plannedMins === null || $plannedMins <= 0) {
            throw new ConflictException(
                'Planned minutes must be greater than zero.'
            );
        }

        try {
            return DB::transaction(
                function () use (
                    $user,
                    $sourceDpa,
                    $targetDate,
                    $plannedMins
                ) {
                    /*
                     * 8. Get or create the target daily plan.
                     */
                    $dailyPlanService = app(
                        DailyPlanService::class
                    );

                    $targetDailyPlan =
                        $dailyPlanService->getOrCreateForDate(
                            $user,
                            $targetDate
                        );

                    /*
                     * 9. Prevent duplicate activity
                     *    in the target daily plan.
                     */
                    $exists = $targetDailyPlan
                        ->activities()
                        ->where(
                            'activity_id',
                            $sourceDpa->activity_id
                        )
                        ->exists();

                    if ($exists) {
                        throw new ConflictException(
                            'Activity already exists in the target daily plan.'
                        );
                    }

                    /*
                     * 10. Create the carried-over activity.
                     */
                    return $targetDailyPlan
                        ->activities()
                        ->create([
                            'activity_id' => $sourceDpa->activity_id,

                            'planned_mins' => $plannedMins,

                            'status' =>
                                DailyPlanActivityStatus::PLANNED,

                            'is_carried_over' => true,

                            'carried_from_id' => $sourceDpa->id,
                        ]);
                }
            );
        } catch (ConflictException $e) {
            /*
             * Our domain exception should not be converted
             * into another exception.
             */
            throw $e;
        } catch (QueryException $e) {
            /*
             * Database-level protection.
             *
             * This protects against a race condition where
             * two requests pass the application-level
             * duplicate check at almost the same time.
             */
            if (
                $e->getCode() === '23000'
                && str_contains(
                    $e->getMessage(),
                    'Duplicate entry'
                )
            ) {
                throw new ConflictException(
                    'Activity already exists in the target daily plan.'
                );
            }

            /*
             * Any other database error should remain
             * a QueryException.
             */
            throw $e;
        }
    }
}