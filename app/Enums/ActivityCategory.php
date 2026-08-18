<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case WORK = 'work';
    case LIFE = 'life';
    case LEARN = 'learn';
}