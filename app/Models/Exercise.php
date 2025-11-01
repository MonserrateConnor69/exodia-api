<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // ✅ ADD THIS PROPERTY TO THE FILE
    protected $fillable = [
        'name',
        'muscle_group_id',
    ];

    /**
     * Get the muscle group that the exercise belongs to.
     */
    public function muscleGroup()
    {
        return $this->belongsTo(MuscleGroup::class);
    }
}