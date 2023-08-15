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

    public function createTokenByUser(string $name, array $abilities, $user): array
    {
        $notPassingAbilities = $this->checkForNotPassingAbilitiesByUser($abilities, $user);

        if ($notPassingAbilities) {
            return [
                [
                    'message' => 'Abilities not valid',
                    'notPassingAbilities' => $notPassingAbilities
                ],
                null
            ];
        }
        return [
            null,
            $user->createToken($name, $abilities)->plainTextToken
        ];
    }

    public function createTokenByToken(string $name, array $abilities, $token): array
    {
        $notPassingAbilities = $this->checkForNotPassingAbilitiesByToken($abilities, $token);

        if ($notPassingAbilities) {
            return [
                [
                    'message' => 'Abilities not valid',
                    'notPassingAbilities' => $notPassingAbilities
                ],
                null
            ];
        }
        return [
            null,
            $token->createToken($name, $abilities)->plainTextToken
        ];
    }

    public function checkForNotPassingAbilitiesByToken(array $abilities, $token): array|bool
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
        if (empty($notPassing)) return false;
        return $notPassing;
    }

    public function checkForNotPassingAbilitiesByUser(array $abilities, $user): array|bool
    {
        $notPassing = [];

        foreach ($abilities as $ability) {
            if (!in_array($ability, $this->abilities)) {
                $notPassing[] = $ability;
            }
            if (isset($this->onlyGrantAbilitiesBy[$ability]) && !$user->hasGlobalRole($this->onlyGrantAbilitiesBy[$ability])) {
                $notPassing[] = $ability;
            }
        }
        if (empty($notPassing)) return false;
        return $notPassing;
    }
}


