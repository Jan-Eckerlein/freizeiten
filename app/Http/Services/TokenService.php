<?php

namespace App\Http\Services;


class TokenService
{
    private $abilities = ['user', 'admin'];

    private $onlyGrantAbilitiesBy = [
        'admin' => ['admin']
    ];

    public function getAbilities()
    {
        return $this->abilities;
    }

    public function getTokens($user)
    {
        return $user->tokens()->get();
    }

    public function getTokenByDeviceName($user, $deviceName)
    {
        return $user->tokens()->where('name', $deviceName)->first();
    }

    public function deleteToken($user, $id)
    {
        return $user->tokens()->where('id', $id)->delete();
    }

    public function deleteAllTokens($user)
    {
        return $user->tokens()->delete();
    }

    public function deleteAllTokensExceptCurrent($user)
    {
        return $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
    }

    public function createToken(string $name, $user): string
    {
        return $user->createToken($name)->plainTextToken;
    }
}


