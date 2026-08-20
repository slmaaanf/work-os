<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;
    protected $fillable = ['goal_id', 'title', 'is_completed'];

    public function goal() {
        return $this->belongsTo(Goal::class);
    }

    public function activities() {
        return $this->hasMany(Activity::class);
    }
}