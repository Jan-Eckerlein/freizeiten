<?php

namespace App\Http\Services;

class RequestService
{
    private array $rules = [
        'username'                  => ['string', 'required', 'unique:users'],
        'first_name'                => ['required', 'string'],
        'last_name'                 => ['required', 'string'],
        'new_email'                 => ['required', 'email', 'unique:users'],
        'email'                     => ['required', 'email'],
        'password'                  => ['required', 'string', 'min:8'],
        'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
        'password_confirmation'     => ['required', 'string', 'min:8'],
        'device_name'               => ['required', 'string', 'min:5', 'max:255', 'regex:/^[a-zA-Z0-9_.-]*$/']
    ];

    public function getRules(): array
    {
        return $this->rules;
    }
}
