<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\TokenResource;
use App\Http\Services\TokenService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function register(AuthRegisterRequest $request, TokenService $tokenService): JsonResponse
    {
        $validated = (object) $request->validated();
        $user = User::create([
            'email'     => $validated->email,
            'password'  => $validated->password,
            'username'  => $validated->username,
            'firstName' => $validated->first_name,
            'lastName'  => $validated->last_name,
        ]);

        $plainTextToken = $tokenService->createToken($validated->device_name, $user);
        $token = TokenResource::make($tokenService->getTokenByDeviceName($user, $validated->device_name));

        return response()->json([
            'message'        => 'Successfully registered new User',
            'email'          => $user->email,
            'plainTextToken' => $plainTextToken,
            'token'          => $token,
        ]);
    }
    public function login(LoginRequest $request, TokenService $tokenService) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();

            $plainTextToken = $tokenService->createToken($request->device_name, $user);
            $token = TokenResource::make($tokenService->getTokenByDeviceName($user, $request->device_name));

            return response()->json([
                'message'        => 'Successfully authenticated',
                'email'          => $user->email,
                'plainTextToken' => $plainTextToken,
                'token'          => $token,
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    public function check(Request $request): JsonResponse
    {
        $token = TokenResource::make($request->user()->currentAccessToken());

        return response()->json([
            'message' => 'Authenticated',
            'email'   => Auth::user()->email,
            'token'   => $token,
        ]);
    }

    public function getTokens(Request $request): JsonResponse
    {
        $tokens = TokenResource::collection($request->user()->tokens()->get());

        return response()->json([
            'message' => 'Successfully retrieved tokens',
            'tokens'  => $tokens,
        ]);
    }

    public function deleteToken($id, Request $request): JsonResponse
    {
        if ($request->user()->tokens()->where('id', $id)->delete()) {
            return response()->json([
                'message' => 'Successfully deleted token',
            ]);
        }

        return response()->json([
            'message' => 'Failed to delete token! Token might not exist or you do not have permission to delete it.',
        ], 401);
    }
}
