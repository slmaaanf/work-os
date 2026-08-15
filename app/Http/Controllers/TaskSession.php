<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'planned_minutes', 'actual_minutes', 
        'started_at', 'ended_at', 'status', 'notes'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}