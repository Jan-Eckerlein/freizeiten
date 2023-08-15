<?php

namespace App\Http\Services;

class RequestService
{
    private array $rules = [
        'username'                  => ['string', 'unique:users'],
        'first_name'                => ['string'],
        'last_name'                 => ['string'],
        'new_email'                 => ['email', 'unique:users'],
        'email'                     => ['email'],
        'password'                  => ['string', 'min:8'],
        'new_password'              => ['string', 'min:8', 'confirmed'],
        'password_confirmation'     => ['string', 'min:8'],
        'device_name'               => ['string', 'min:5', 'max:255', 'regex:/^[a-zA-Z0-9_.-]*$/'],
        'abilities'                 => ['array', ],
        'token'                     => ['string'],
    ];

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRule(string $rule): array
    {
        return $this->rules[$rule];
    }

    public function getRuleRequired(string $rule): array
    {
        return array_merge($this->rules[$rule], ['required']);
    }

    public function getRuleRequiredWith(string $rule, array $with): array
    {
        return array_merge($this->rules[$rule], ['required'], $with);
    }

    public function getRuleWith(string $rule, array $with): array
    {
        return array_merge($this->rules[$rule], $with);
    }
}
