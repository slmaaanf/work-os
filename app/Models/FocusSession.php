<?php

namespace App\Models;

use App\Enums\FocusSessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_plan_activity_id', 'status', 'started_at', 'paused_at', 
        'accumulated_pause_seconds', 'finished_at', 'actual_duration_seconds', 'result'
    ];

    protected function casts(): array
    {
        return [
            'status' => FocusSessionStatus::class,
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function dailyPlanActivity(): BelongsTo
    {
        return $this->belongsTo(DailyPlanActivity::class);
    }

    /**
     * Helper Domain: Menghitung durasi bersih (Net Duration)
     */
    public function actualDurationSeconds(): ?int
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }

        $pauseSeconds = $this->accumulated_pause_seconds;

        // Jika user klik FINISH saat masih dalam state PAUSED
        if ($this->paused_at) {
            $pauseSeconds += $this->paused_at->diffInSeconds($this->finished_at);
        }

        return max(0, $this->started_at->diffInSeconds($this->finished_at) - $pauseSeconds);
    }
}