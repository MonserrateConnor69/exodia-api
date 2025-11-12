<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    // Hardcoded muscle groups
    private static $muscleGroupsStatic = [
        1 => 'Chest',
        2 => 'Back',
        3 => 'Core',
        4 => 'Shoulders',
        5 => 'Biceps',
        6 => 'Triceps',
        7 => 'Quads',
        8 => 'Hamstrings',
        9 => 'Calves',
    ];

    private function callGemini(string $prompt)
    {
        $apiKey = config('gemini.api_key');
        
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $response = Http::post($url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini API request failed: ' . $response->body());
        }
        
        return $response->json('candidates.0.content.parts.0.text');
    }

    // Updated method
    public function generateWorkout(int $muscleGroupId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userDetails = "Age: {$user->age}, Gender: {$user->gender}, Weight: {$user->weight} lbs, Height: {$user->height} inches.";

        // Look up muscle name from static array
        $muscleName = self::$muscleGroupsStatic[$muscleGroupId] ?? 'Unknown Muscle';

        $prompt = "
            A user with the following profile wants a workout plan: {$userDetails}.
            They want to train their {$muscleName} today.
            Generate a personalized workout for them.
            Include for each exercise:
                - sets
                - reps
                - a one-sentence description of how it is performed or what it targets
            Respond ONLY with a valid JSON object in the following format, with NO other text or markdown formatting:
            { 
                \"exercises\": [ 
                    { 
                        \"name\": \"Exercise Name 1\", 
                        \"sets\": 3, 
                        \"reps\": \"8-12\", 
                        \"description\": \"Short description here.\" 
                    }, 
                    { 
                        \"name\": \"Exercise Name 2\", 
                        \"sets\": 3, 
                        \"reps\": \"10-15\", 
                        \"description\": \"Short description here.\" 
                    } 
                ] 
            }
        ";

        try {
            $content = $this->callGemini($prompt);

            if (!$content) {
                return response()->json(['error' => 'The AI returned an empty response.'], 500);
            }

            $cleanedContent = preg_replace('/^```json\s*|\s*```$/', '', $content);
            $jsonResponse = json_decode($cleanedContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Failed to parse AI workout response.', 'raw_response' => $content], 500);
            }

            return response()->json($jsonResponse);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'The AI service failed with a specific error.',
                'exception_message' => $e->getMessage(),
            ], 500);
        }
    }

    // Diet functions remain unchanged
    public function generateDiet()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userDetails = "Age: {$user->age}, Gender: {$user->gender}, Weight: {$user->weight} lbs, Height: {$user->height} inches.";
        $recentWorkouts = $user->workoutLogs()->with('exercise')->latest()->take(3)->get();
        $workoutHistory = $recentWorkouts->map(fn($log) => $log->exercise->name)->implode(', ');

        $prompt = "
            A user with the following profile needs a diet recommendation: {$userDetails}.
            Their most recent workouts were: {$workoutHistory}.
            Generate a single, healthy meal recommendation.
            Respond ONLY with a valid JSON object in the following format, with NO other text or markdown formatting:
            { \"meal\": { \"name\": \"Meal Name\", \"description\": \"Short description.\", \"foodItems\": [\"Food 1\", \"Food 2\"], \"estimatedCalories\": 500 } }
        ";

        try {
            $content = $this->callGemini($prompt);

            if (!$content) {
                return response()->json(['error' => 'The AI returned an empty response.'], 500);
            }

            $cleanedContent = preg_replace('/^```json\s*|\s*```$/', '', $content);
            $jsonResponse = json_decode($cleanedContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Failed to parse AI diet response.', 'raw_response' => $content], 500);
            }

            return response()->json($jsonResponse);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'The AI service failed with a specific error.',
                'exception_message' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateDietRecommendation()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userDetails = "Age: {$user->age}, Gender: {$user->gender}, Weight: {$user->weight} lbs, Height: {$user->height} inches.";

        $trainedMuscleGroupIds = WorkoutLog::where('user_id', $user->id)
            ->join('exercises', 'workout_logs.exercise_id', '=', 'exercises.id')
            ->select('exercises.muscle_group_id')
            ->distinct()
            ->pluck('muscle_group_id');
        
        $trainedMuscles = [];
        foreach ($trainedMuscleGroupIds as $id) {
            $trainedMuscles[] = self::$muscleGroupsStatic[$id] ?? 'Unknown Muscle';
        }
        $trainedMuscles = implode(', ', $trainedMuscles);

        if (empty($trainedMuscles)) {
            return response()->json(['error' => 'No workout history found to base a diet on.'], 400);
        }

        $prompt = "
            A user with the following profile needs a one-day diet plan: {$userDetails}.
            The diet plan should be optimized for muscle recovery and growth, focusing on these muscle groups they have trained: {$trainedMuscles}.
            
            IMPORTANT: The user is in the Philippines. All recommended meals and ingredients must be commonly available, 
            affordable, and culturally relevant in the Philippines. Incorporate local staples like rice, chicken (manok), fish (isda),
            lean pork (baboy), local vegetables (like kangkong, malunggay, talong), and fruits (like bananas, mangoes)

            Please provide a simple, healthy meal plan for Breakfast, Lunch, and Dinner.
            For each meal, give a brief description and a list of the main food items.
            Structure the entire response as plain text, using clear headings for each meal. Do not use markdown like '```' or JSON formatting.
        ";

        try {
            $content = $this->callGemini($prompt);

            if (!$content) {
                return response()->json(['error' => 'The AI returned an empty response.'], 500);
            }

            return response()->json(['diet_plan' => $content]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'The AI service failed with a specific error.',
                'exception_message' => $e->getMessage(),
            ], 500);
        }
    }
}
