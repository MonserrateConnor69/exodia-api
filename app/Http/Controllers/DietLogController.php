<?php

namespace App\Http\Controllers;

use App\Models\DietLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DietLogController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $dietLogs = $user->dietLogs()->whereDate('created_at', today())->get();
        return response()->json($dietLogs);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'food_name' => 'required|string|max:255',
            'calories' => 'required|integer',
            'protein' => 'nullable|numeric',
            'carbs' => 'nullable|numeric',
            'fat' => 'nullable|numeric',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // This is the line that was missing before
        $dietLog = $user->dietLogs()->create($validatedData); 

        return response()->json([
            'message' => 'Diet log created successfully!',
            'diet_log' => $dietLog,
        ], 201);
    }
}