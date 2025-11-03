<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Auth\Access\Response;

class WorkoutLogPolicy
{
   
    public function viewAny(User $user): bool
    {
        return false;
    }

    
    public function view(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    
    public function create(User $user): bool
    {
        return false;
    }

    
    public function update(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    
     public function delete(User $user, WorkoutLog $log): bool
    {
        return $user->id === $log->user_id;
    }
    
    public function restore(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    
    public function forceDelete(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }
}
