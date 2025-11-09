<?php

use App\Http\Controllers\AuthController; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DietLogController;
use App\Http\Controllers\AIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// --- Authentication Routes ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/muscle-groups', [WorkoutController::class, 'getMuscleGroups']);
Route::get('/muscle-groups/{muscleGroup}/exercises', [WorkoutController::class, 'getExercisesForMuscleGroup']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/vitals', [UserController::class, 'updateVitals']);

    Route::post('/workout-logs', [WorkoutController::class, 'storeWorkoutLog']);
    Route::get('/workout-logs', [WorkoutController::class, 'getTodaysLogs']);

    Route::get('/recovery-states', [WorkoutController::class, 'getRecoveryStates']);
    Route::post('/next-day', [WorkoutController::class, 'advanceDay']);
    Route::delete('/workout-logs/muscle/{muscleGroup}', [WorkoutController::class, 'deleteTodaysLogsForMuscle']);


    Route::get('/diet-logs', [DietLogController::class, 'index']);
    Route::post('/diet-logs', [DietLogController::class, 'store']);
    
    Route::get('/ai/workout/{muscleGroup}', [AIController::class, 'generateWorkout']);
    Route::get('/ai/diet', [AIController::class, 'generateDiet']);

    Route::post('/diet-recommendation', [AIController::class, 'generateDietRecommendation']);

     Route::get('/workout-logs/muscle/{muscleGroup}', [WorkoutController::class, 'getTodaysLogsForMuscle']);
     Route::delete('/workout-logs/{log}', [WorkoutController::class, 'destroy']);


});