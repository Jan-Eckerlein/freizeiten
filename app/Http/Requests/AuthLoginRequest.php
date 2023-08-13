<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
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
            'email'       => $rs->getRules()['email'],
            'password'    => $rs->getRules()['password'],
            'device_name' => $rs->getRules()['device_name'],
        ];
    }
}
