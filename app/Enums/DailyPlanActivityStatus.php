<?php

namespace App\Enums;

enum DailyPlanActivityStatus: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case DONE_TODAY = 'done_today';
}