<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPlanActivity extends Model
{
    use HasFactory;

    // INI BAGIAN PALING PENTING: Mengizinkan Laravel menyimpan data ke kolom ini
    protected $fillable = [
        'daily_plan_id',
        'activity_id',
        'planned_mins',
        'actual_mins',
        'achievements',
        'blockers',
        'status'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function dailyPlan()
    {
        return $this->belongsTo(DailyPlan::class);
    }
}