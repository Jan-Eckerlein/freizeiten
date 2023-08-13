<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(RequestService $rs): array
    {
        return [
            'username'              => $rs->getRules()['username'],
            'first_name'            => $rs->getRules()['first_name'],
            'last_name'             => $rs->getRules()['last_name'],
            'email'                 => $rs->getRules()['email'],
            'password'              => $rs->getRules()['password'],
            'password_confirmation' => $rs->getRules()['password_confirmation'],
            'device_name'           => $rs->getRules()['device_name']
        ];
    }
}
