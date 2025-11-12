<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLog;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WorkoutController extends Controller
{
    // ✅ FIX 1: Remove nested eager loading that crashes on empty MuscleGroup table.
    public function getRecoveryStates()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $recoveringLogs = $user->workoutLogs()
            ->where('recovery_stage', '<', 4)
            // CRITICAL FIX: Only load 'exercise', not the nested 'muscleGroup'
            ->with('exercise') 
            ->get();

        $recoveryStates = [];

        foreach ($recoveringLogs as $log) {
            // Check that the exercise relationship exists
            if ($log->exercise) {
                // Get the muscle ID directly from the exercise model
                $muscleId = $log->exercise->muscle_group_id;
                
                // Use the log's recovery stage (which you previously fixed)
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
        // HARDCODED DATA IS CORRECT
        $muscleGroups = [
            ['id' => 1, 'name' => 'Chest', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'name' => 'Back', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'name' => 'Core', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'name' => 'Shoulders', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'name' => 'Biceps', 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'name' => 'Triceps', 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'name' => 'Quads', 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'name' => 'Hamstrings', 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'name' => 'Calves', 'created_at' => null, 'updated_at' => null],
        ];
        
        return response()->json($muscleGroups, 200);
    }

    // Route Model Binding Bypassed
    public function getExercisesForMuscleGroup(int $muscleGroupId)
    {
        return response()->json([]);
    }

    // ✅ FIX: Using cache-based storage instead of database
    public function storeWorkoutLog(Request $request)
    {
        $validated = $request->validate([
            'exercise_name' => 'required|string|max:255',
            'muscle_group_id' => 'required|integer', 
            'date' => 'sometimes|date_format:Y-m-d',
        ]);

        $userId = Auth::id();
        $cacheKey = "workout_logs_{$userId}";
        
<<<<<<< HEAD
        // Get existing logs from cache
        $logs = Cache::get($cacheKey, []);
        
        // Generate a unique ID for this log (using timestamp + random)
        $logId = time() . '_' . uniqid();
        
        // Check if a log already exists for this exercise and user (updateOrCreate logic)
        $existingLogKey = null;
        foreach ($logs as $key => $log) {
            if ($log['exercise_name'] === $validated['exercise_name'] && 
                $log['user_id'] === $userId) {
                $existingLogKey = $key;
                break;
            }
        }
        
        $createdAt = isset($validated['date']) 
            ? Carbon::parse($validated['date'])->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');
        
        $logData = [
            'id' => $existingLogKey ? $logs[$existingLogKey]['id'] : $logId,
            'user_id' => $userId,
            'exercise_name' => $validated['exercise_name'],
            'muscle_group_id' => $validated['muscle_group_id'],
            'recovery_stage' => $existingLogKey ? $logs[$existingLogKey]['recovery_stage'] : 1,
            'created_at' => $createdAt,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
        
        if ($existingLogKey !== null) {
            // Update existing log
            $logs[$existingLogKey] = $logData;
        } else {
            // Create new log
            $logs[] = $logData;
        }
        
        // Store back in cache (24 hour expiration)
        Cache::put($cacheKey, $logs, now()->addHours(24));
=======
       
       $log = WorkoutLog::updateOrCreate(
    [
        'user_id' => Auth::id(),
        'exercise_id' => $exercise->id,
    ],
    [
        'recovery_stage' => 1, // ✅ Correctly set to Stage 1 (Red)
        'created_at' => isset($validated['date']) ? Carbon::parse($validated['date']) : now()
    ]
);

>>>>>>> temp-laravel-fix
        
        return response()->json([
            'message' => 'Workout logged successfully!',
            'workout_log' => $logData,
            'exercise_name' => $validated['exercise_name']
        ], 201);
    }

    public function getTodaysLogs(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        
        // Get logs from cache instead of database
        $cacheKey = "workout_logs_{$userId}";
        $allLogs = Cache::get($cacheKey, []);
        
        // Filter logs for the requested date
        $logs = array_filter($allLogs, function($log) use ($date, $userId) {
            $logDate = Carbon::parse($log['created_at'])->format('Y-m-d');
            $requestDate = $date->format('Y-m-d');
            return $logDate === $requestDate && $log['user_id'] === $userId;
        });
        
        // Format logs to include exercise information
        $formattedLogs = array_map(function($log) {
            return [
                'id' => $log['id'],
                'user_id' => $log['user_id'],
                'exercise_id' => null, // Not using exercise_id anymore
                'exercise_name' => $log['exercise_name'],
                'muscle_group_id' => $log['muscle_group_id'],
                'recovery_stage' => $log['recovery_stage'],
                'created_at' => $log['created_at'],
                'updated_at' => $log['updated_at'] ?? null,
                'exercise' => [
                    'id' => null,
                    'name' => $log['exercise_name'],
                    'muscle_group_id' => $log['muscle_group_id'],
                ]
            ];
        }, array_values($logs));
            
        return response()->json($formattedLogs);
    }

    public function destroy(WorkoutLog $log)
    {
        $this->authorize('delete', $log);
        $log->delete();
        return response()->json(['message' => 'Workout log removed successfully.']);
    }

    // Route Model Binding Bypassed
    public function getTodaysLogsForMuscle(int $muscleGroupId)
    {
        $userId = Auth::id();
        $exerciseIds = Exercise::where('muscle_group_id', $muscleGroupId)->pluck('id');

        // CRITICAL FIX: Only load 'exercise', not the nested 'muscleGroup'
        $logs = WorkoutLog::where('user_id', $userId)
            ->whereIn('exercise_id', $exerciseIds)
            ->whereDate('created_at', Carbon::today())
            ->with('exercise')
            ->get();

        return response()->json($logs);
    }

    // NEW: Delete today's logs for a specific muscle group
    public function deleteTodaysLogsForMuscle(int $muscleGroupId)
    {
        $userId = Auth::id();
        $exerciseIds = Exercise::where('muscle_group_id', $muscleGroupId)->pluck('id');

        WorkoutLog::where('user_id', $userId)
            ->whereIn('exercise_id', $exerciseIds)
            ->whereDate('created_at', Carbon::today())
            ->delete();

        return response()->json([
            'message' => 'Today\'s workout logs for muscle group ' . $muscleGroupId . ' deleted successfully.'
        ]);
    }
}