<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Update the authenticated user's vitals.
     */
    public function updateVitals(Request $request)
    {
        // 1. Validate the incoming data to make sure it's in the right format.
        $validatedData = $request->validate([
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:255',
        ]);

       /** @var \App\Models\User $user */
        $user = Auth::user();

        // 3. Update the user's information with the validated data.
        $user->update($validatedData);

        // 4. Return a success response with the updated user data.
        return response()->json([
            'message' => 'Vitals updated successfully!',
            'user' => $user,
        ]);
    }
}