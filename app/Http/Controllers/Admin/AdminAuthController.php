<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Resources\TokenResource;
use App\Http\Services\TokenService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AdminAuthController extends Controller
{
    public function createAdminToken (AuthLoginRequest $request, TokenService $tokenService)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->whereGlobalRole('admin')->first();
            if (!$user) {
                return response()->json([
                    'message' => 'The provided credentials do not match our records.',
                ], 401);
            }
            
            $plainTextToken = $user->createToken($request->device_name, ['admin'])->plainTextToken;
            $token = TokenResource::make($tokenService->getTokenByDeviceName($user, $request->device_name));

            return response()->json([
                'message'        => 'Successfully created Admintoken',
                'email'          => $request->email,
                'plainTextToken' => $plainTextToken,
                'token'          => $token,
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }
}
