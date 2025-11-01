<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // ✅ ADD THIS PROPERTY TO THE FILE
    protected $fillable = [
        'user_id',
        'exercise_id',
    ];

    /**
     * Get the user that owns the workout log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exercise associated with the workout log.
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}