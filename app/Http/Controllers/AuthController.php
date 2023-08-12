<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        auth()->login($user);

        return response()->json([
            'message' => 'Successfully registered new User',
            'email' => $user->email,
        ]);
    }

    // public function authenticate(Request $request): JsonResponse
    // {
    //     $credentials = $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required'],
    //     ]);

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();

    //         return redirect()->intended('dashboard');
    //     }

    //     return back()->withErrors([
    //         'email' => 'The provided credentials do not match our records.',
    //     ])->onlyInput('email');
    // }
}
