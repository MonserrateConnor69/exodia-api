<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});


Route::get('/test-openai', function() {
    try {
        $response = Http::withToken(config('openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => 'Say hello in one sentence.']
                ],
                'max_tokens' => 10,
            ]);

        return $response->json();
    } catch (\Exception $e) {
        return [
            'error' => true,
            'message' => $e->getMessage(),
        ];
    }


    
});
