<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    // ✅ CRITICAL FIX: This stops Eloquent from crashing when setting timestamps.
    public $timestamps = false; 

    protected $fillable = [
        'name',
        'muscle_group_id',
    ];

    public function muscleGroup()
    {
        return $this->belongsTo(MuscleGroup::class);
    }
}