<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    use HasFactory;

    // ✅ CRITICAL FIX: This stops Eloquent from crashing when setting timestamps.
    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'exercise_id',
        'recovery_stage',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}