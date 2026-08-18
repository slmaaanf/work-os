<?php

namespace App\Models;

use App\Enums\DailyPlanActivityStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyPlanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_plan_id',
        'activity_id',
        'planned_mins',
        'status',
        'is_carried_over',
        'carried_from_id',
    ];

    protected $casts = [
        'status' => DailyPlanActivityStatus::class,
        'is_carried_over' => 'boolean',
    ];

    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function carriedFrom(): BelongsTo
    {
        return $this->belongsTo(
            DailyPlanActivity::class,
            'carried_from_id'
        );
    }

    public function carriedTo(): HasMany
    {
        return $this->hasMany(
            DailyPlanActivity::class,
            'carried_from_id'
        );
    }

    public function focusSessions(): HasMany
    {
        return $this->hasMany(FocusSession::class);
    }
}