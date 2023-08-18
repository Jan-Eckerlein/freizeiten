<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterRequest extends FormRequest
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
            'username'              => $rs->getRuleRequired('username'),
            'first_name'            => $rs->getRuleRequired('first_name'),
            'last_name'             => $rs->getRuleRequired('last_name'),
            'email'                 => $rs->getRuleRequired('new_email'),
            'password'              => $rs->getRuleRequired('new_password'),
            'password_confirmation' => $rs->getRuleRequired('password_confirmation'),
            'device_name'           => $rs->getRuleRequired('device_name')
        ];
    }
}
