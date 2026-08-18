<?php

namespace App\Enums;

enum FocusSessionStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';
}