<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class OrganizationCreateRequest extends FormRequest
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
            'name'        => $rs->getRuleRequired('organization_name'),
            'owner_id'    => $rs->getRuleRequired('organization_onwer_id'),
            'user_ids'    => $rs->getRule('organization_user_ids'),
            'user_ids.*'  => $rs->getRule('organization_user_ids.*'),
            'admin_ids'   => $rs->getRule('organization_admin_ids'),
            'admin_ids.*' => $rs->getRule('organization_admin_ids.*'),
        ];
    }
}
