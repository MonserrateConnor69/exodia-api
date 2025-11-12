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
    
    protected $fillable = [
        'user_id',
        'exercise_id',
        'recovery_stage', 
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