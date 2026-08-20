<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'category', 'status', 'color'];

    // INI YANG TADI TERLEWAT: Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    // Menghitung persentase otomatis
    public function getProgressAttribute()
    {
        $total = $this->milestones()->count();
        if ($total === 0) return 0;
        
        $completed = $this->milestones()->where('is_completed', true)->count();
        return round(($completed / $total) * 100);
    }
}