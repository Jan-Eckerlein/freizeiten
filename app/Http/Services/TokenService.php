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

    public function createToken($user, $token = null, $abilities = ['user']): array
    {
        $notPassingAbilities = $this->validateAbilities($abilities, $token);
        if ($notPassingAbilities !== true) {
            return [
                [
                    'message' => 'Abilities not valid',
                    'notPassingAbilities' => $notPassingAbilities
                ],
                null
            ];
        }
        return [
            false,
            $user->createToken($token, $abilities)->plainTextToken
        ];
    }

    public function validateAbilities(array $abilities, $token): array|bool
    {
        // abilities need to be in the array of abilities
        // and if token ist provided also included in its abilities list
        $notPassing = [];

        foreach ($abilities as $ability) {
            if (!in_array($ability, $this->abilities)) {
                $notPassing[] = $ability;
            }
            if (isset($this->onlyGrantAbilitiesBy[$ability]) && !in_array($ability, $token->abilities)) {
                $notPassing[] = $ability;
            }
        }
        if (empty($notPassing)) return true;
        return $notPassing;
    }
}


