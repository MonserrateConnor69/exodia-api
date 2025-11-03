<?php



namespace App\Http\Controllers;

use App\Models\MuscleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
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

    public function generateWorkout(MuscleGroup $muscleGroup)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userDetails = "Age: {$user->age}, Gender: {$user->gender}, Weight: {$user->weight} lbs, Height: {$user->height} inches.";

      $prompt = "
    A user with the following profile wants a workout plan: {$userDetails}.
    They want to train their {$muscleGroup->name} today.
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
}