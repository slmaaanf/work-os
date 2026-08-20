<?php

namespace App\Enums;

enum ActivityCategory: string
{
    case CIMORY = 'cimory';
    case WORK = 'work';
    case PERSONAL = 'personal';

    public function label(): string
    {
        return match($this) {
            self::CIMORY => '🏢 Cimory',
            self::WORK => '💻 Work',
            self::PERSONAL => '🌱 Personal',
        };
    }
}