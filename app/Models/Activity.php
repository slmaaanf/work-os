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

    // UPDATE: Tambahkan 'goal_id' dan 'milestone_id' agar diizinkan masuk ke database
    protected $fillable = [
        'user_id', 
        'project_id', 
        'goal_id',       // Tambahan untuk fitur Goals
        'milestone_id',  // Tambahan untuk fitur Milestones
        'title', 
        'category', 
        'status', 
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'status' => ActivityStatus::class,
            'category' => ActivityCategory::class,
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Project bawaanmu
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi ke DailyPlanActivity
    public function dailyPlanActivities(): HasMany
    {
        return $this->hasMany(DailyPlanActivity::class);
    }

    // ==========================================
    // RELASI BARU UNTUK FITUR GOALS & MILESTONES
    // ==========================================

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}