<?php

namespace App\Http\Requests;

use App\Http\Services\RequestService;
use Illuminate\Foundation\Http\FormRequest;

class OrganizationUpdateRequest extends FormRequest
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
            'name'        => $rs->getRuleRequiredWithout('organization_name', ['owner_id']),
            'owner_id'    => $rs->getRuleRequiredWithout('organization_onwer_id', ['name']),
            'user_ids'    => $rs->getRule('organization_user_ids'),
            'user_ids.*'  => $rs->getRule('organization_user_ids.*'),
            'admin_ids'   => $rs->getRule('organization_admin_ids'),
            'admin_ids.*' => $rs->getRule('organization_admin_ids.*'),
        ];
    }
}
