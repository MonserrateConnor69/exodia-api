<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Add this line

class MuscleGroup extends Model
{
    use HasFactory;

    /**
     * Get the exercises for the muscle group.
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
