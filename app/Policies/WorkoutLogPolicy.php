<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Auth\Access\Response;

class WorkoutLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
     public function delete(User $user, WorkoutLog $log): bool
    {
        return $user->id === $log->user_id;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkoutLog $workoutLog): bool
    {
        return false;
    }
}
