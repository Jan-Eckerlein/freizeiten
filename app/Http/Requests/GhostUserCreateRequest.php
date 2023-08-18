<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class GhostUserCreateRequest extends FormRequest
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
            'username'  => $rs->getRuleRequired('username'),
            'nickname'  => $rs->getRule('nickname'),
            'firstName' => $rs->getRuleRequired('first_name'),
            'lastName'  => $rs->getRuleRequired('last_name'),
        ];
    }
}
