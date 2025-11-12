<?php

namespace App\Http\Controllers;

use App\Models\MuscleGroup;
use App\Models\WorkoutLog;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkoutController extends Controller
{
  
public function getRecoveryStates()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $recoveringLogs = $user->workoutLogs()
        ->where('recovery_stage', '<', 4)
        ->with('exercise.muscleGroup') 
        ->get();

    $recoveryStates = [];

    foreach ($recoveringLogs as $log) {
        if ($log->exercise && $log->exercise->muscleGroup) {
            $muscleId = $log->exercise->muscleGroup->id;
            $currentStage = $log->recovery_stage;

           
            if (!isset($recoveryStates[$muscleId]) || $currentStage < $recoveryStates[$muscleId]) {
                $recoveryStates[$muscleId] = $currentStage;
            }
        }
    }

    return response()->json($recoveryStates);
}

     public function advanceDay()
    {
        $userId = Auth::id();

        WorkoutLog::where('user_id', $userId)->where('recovery_stage', '>=', 4)->delete();

        WorkoutLog::where('user_id', $userId)->increment('recovery_stage');
        
        return response()->json(['message' => 'Recovery state advanced for all muscles.']);
    }

    public function getMuscleGroups()
    {
        return response()->json(MuscleGroup::all());
    }

    
    public function getExercisesForMuscleGroup(MuscleGroup $muscleGroup)
    {
        return $muscleGroup->exercises;
    }

   
    public function storeWorkoutLog(Request $request)
    {
        $validated = $request->validate([
            'exercise_name' => 'required|string|max:255',
            'muscle_group_id' => 'required|exists:muscle_groups,id',
            'date' => 'sometimes|date_format:Y-m-d',
        ]);

        $exercise = Exercise::firstOrCreate(
            ['name' => $validated['exercise_name']],
            ['muscle_group_id' => $validated['muscle_group_id']]
        );
        
       
        $log = WorkoutLog::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'exercise_id' => $exercise->id,
            ],
            [
                'recovery_stage' => 1, // Start recovery
                'created_at' => isset($validated['date']) ? Carbon::parse($validated['date']) : now()
            ]
        );
        
        $log->load('exercise');

        return response()->json([
            'message' => 'Workout logged successfully!',
            'workout_log' => $log
        ], 201);
    }

    public function getTodaysLogs(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        
        $logs = WorkoutLog::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->with('exercise.muscleGroup')
            ->get();
            
        return response()->json($logs);
    }

  
    public function destroy(WorkoutLog $log)
    {
       
          // $this->authorize('delete', $log); // <--- REMOVE OR COMMENT THIS OUT
    if ($log->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    $log->delete();
    return response()->json(['message' => 'Workout log removed successfully.']);
}


    
    public function getTodaysLogsForMuscle(MuscleGroup $muscleGroup)
    {
        $userId = Auth::id();
        $exerciseIds = $muscleGroup->exercises()->pluck('id');

        $logs = WorkoutLog::where('user_id', $userId)
            ->whereIn('exercise_id', $exerciseIds)
            ->whereDate('created_at', Carbon::today())
            ->with('exercise')
            ->get();

        return response()->json($logs);
    }
}