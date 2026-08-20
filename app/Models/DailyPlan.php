<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in_at',  // Tambahan baru
        'clock_out_at', // Tambahan baru
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in_at' => 'datetime',  // Cast otomatis ke instance Carbon
            'clock_out_at' => 'datetime', // Cast otomatis ke instance Carbon
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DailyPlanActivity::class);
    }
}