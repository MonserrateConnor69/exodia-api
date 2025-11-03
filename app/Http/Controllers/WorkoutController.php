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
    
    public function getMuscleGroups()
    {
        return MuscleGroup::all();
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
        
        $log = new WorkoutLog();
        $log->user_id = Auth::id();
        $log->exercise_id = $exercise->id;
        if (isset($validated['date'])) {
            $log->created_at = Carbon::parse($validated['date']);
        }
        $log->save();
        
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
            
        $groupedLogs = $logs->groupBy('exercise.muscleGroup.id');
        return response()->json($groupedLogs);
    }

  
    public function destroy(WorkoutLog $log)
    {
        $this->authorize('delete', $log);
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
