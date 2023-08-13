<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Services\RequestService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $validated = (object) $request->validated();
        $user = User::create([
            'email'     => $validated->email,
            'password'  => $validated->password,
            'username'  => $validated->username,
            'firstName' => $validated->first_name,
            'lastName'  => $validated->last_name,
        ]);

        $token = $user->createToken($validated->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Successfully registered new User',
            'email'   => $user->email,
            'token'   => $token,
        ]);
    }

    public function login(AuthLoginRequest $request, RequestService $rs): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $plainTextToken = $user->createToken($request->device_name)->plainTextToken;

            $token = $user->tokens()->where('name', $request->device_name)->first();


            return response()->json([
                'message' => 'Successfully authenticated',
                'email'   => $request->email,
                'token'   => $plainTextToken,
                'token_name'      => $token->name,
                'token_abilities' => $token->abilities,
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 422);
    }

    public function check(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();

        return response()->json([
            'message'         => 'Authenticated',
            'email'           => Auth::user()->email,
            'token_name'      => $currentToken->name,
            'token_abilities' => $currentToken->abilities,
        ]);
    }
}
