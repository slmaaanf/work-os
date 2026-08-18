<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'project_id', 'title', 'category', 'status', 'completed_at'];

    protected function casts(): array
    {
        return [
            'status' => ActivityStatus::class,
            'category' => ActivityCategory::class,
            'completed_at' => 'datetime',
        ];
    }

    // ... (Relasi tetap sama seperti sebelumnya)
}