<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // $request->session()->regenerate();
            return response()->json([
                'message' => 'Successfully authenticated',
                'email' => $request->email,
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 422);
    }

    public function check(): JsonResponse
    {
        return Auth::check()
            ? response()->json([
                'message' => 'User is logged in',
                'email' => Auth::user()->email,
            ])
            : response()->json([
                'message' => 'User is not logged in',
            ], 422);
    }
}
