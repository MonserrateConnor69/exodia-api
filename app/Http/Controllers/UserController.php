<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    public function updateVitals(Request $request)
    {
        $validatedData = $request->validate([
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:255',
        ]);

       /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update($validatedData);

        return response()->json([
            'message' => 'Vitals updated successfully!',
            'user' => $user,
        ]);
    }
}