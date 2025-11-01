<?php

use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DietLogController;
use App\Http\Controllers\AIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Authentication Routes ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- Public Workout Routes ---
Route::get('/muscle-groups', [WorkoutController::class, 'getMuscleGroups']);
// Note: We are keeping this old route for now, but the AI will replace its function.
Route::get('/muscle-groups/{muscleGroup}/exercises', [WorkoutController::class, 'getExercisesForMuscleGroup']);


// --- Protected Routes (Requires Token) ---
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/vitals', [UserController::class, 'updateVitals']);

    // Workout routes
    Route::post('/workout-logs', [WorkoutController::class, 'storeWorkoutLog']);

      // ✅ ADD THESE TWO NEW ROUTES FOR THE NEW FEATURES
    Route::get('/workout-logs', [WorkoutController::class, 'getTodaysLogs']);
    Route::delete('/workout-logs/muscle/{muscleGroup}', [WorkoutController::class, 'deleteTodaysLogsForMuscle']);


    // Diet routes
    Route::get('/diet-logs', [DietLogController::class, 'index']);
    Route::post('/diet-logs', [DietLogController::class, 'store']);
    
    // --- AI Coach Routes (The New Plan!) ---
    // This route will generate a workout for a specific muscle
    Route::get('/ai/workout/{muscleGroup}', [AIController::class, 'generateWorkout']);
    
    // This route will generate a diet plan based on user history
    Route::get('/ai/diet', [AIController::class, 'generateDiet']);

     Route::get('/workout-logs/muscle/{muscleGroup}', [WorkoutController::class, 'getTodaysLogsForMuscle']);
     Route::delete('/workout-logs/{log}', [WorkoutController::class, 'destroy']);


});