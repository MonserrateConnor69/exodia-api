<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
  public function updateVitals(Request $request)
{
    $validatedData = $request->validate([
        'weight' => 'required|numeric|min:50|max:1000',
        'height' => 'required|numeric|min:24|max:96',
        'age'    => 'required|integer|min:13|max:120',
        'gender' => 'required|string|in:Male,Female,Other',
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