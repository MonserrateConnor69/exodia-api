<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validate the incoming data (email and password are required)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Attempt to log the user in
        if (!Auth::attempt($request->only('email', 'password'))) {
            // 3. If login fails, throw an error
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // 4. If login is successful, get the authenticated user
        $user = $request->user();

        // 5. Create a new API token for the user
        $token = $user->createToken('auth-token');

        // 6. Return the user's details and the new token
        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }
}