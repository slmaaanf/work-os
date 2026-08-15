<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Tambahkan 'work_logs' dan 'actual_time' ke dalam fillable
    protected $fillable = [
        'user_id', 'task_date', 'mode', 'category', 'title', 'task_type',
        'project', 'milestone', // <--- TAMBAHAN BARU
        'status', 'effort_score', 'impact_score', 'blocker_tags',
        'work_logs', 'actual_time', 
        'stakeholder', 'environment', 'daily_win', 'oops_moment', 'lesson_learned',
        'severity', 'bug_environment', 'steps_to_reproduce', 
        'expected_result', 'actual_result', 'root_cause', 'solution'
    ];

    // Beritahu Laravel agar mengubah JSON dari database menjadi Array PHP
    protected $casts = [
        'work_logs' => 'array',
        'project' => 'string',
        'milestone' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: 1 Task punya banyak Sesi Timebox
    public function sessions()
    {
        return $this->hasMany(TaskSession::class);
    }
}