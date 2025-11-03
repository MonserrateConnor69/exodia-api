<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class MuscleGroup extends Model
{
    use HasFactory;

    
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
}
