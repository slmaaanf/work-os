<?php

namespace App\Enums;

enum FocusSessionDecision: string
{
    case CONTINUE_LATER = 'continue_later';
    case DONE_TODAY = 'done_today';
    case COMPLETED = 'completed';
}