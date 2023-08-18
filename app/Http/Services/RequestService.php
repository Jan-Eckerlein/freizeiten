<?php

namespace App\Http\Services;

class RequestService
{
    private array $rules = [
        'username'                  => ['string', 'unique:users', 'min:5', 'max:255', 'regex:/^[a-zA-Z0-9_.-]*$/'],
        'nickname'                  => ['string', 'min:5', 'max:255'],
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
        'organization_name'         => ['string', 'unique:organization,name'],
        'organization_onwer_id'     => ['numeric', 'exists:users,id'],
        'organization_user_ids'     => ['array'],
        'organization_user_ids.*'   => ['numeric', 'exists:users,id'],
        'organization_admin_ids'    => ['array'],
        'organization_admin_ids.*'  => ['numeric', 'exists:users,id'],
    ];

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getRule(string $rule, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], $additionalRules);
    }

    public function getRuleRequired(string $rule, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required'], $additionalRules);
    }

    public function getRuleRequiredIf(string $rule, string $anotherField, string $anotherFieldValue, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required_if:' . $anotherField . ',' . $anotherFieldValue], $additionalRules);
    }

    public function getRuleRequiredWith(string $rule, array $presentFields, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required_with:' . implode(',', $presentFields)], $additionalRules);
    }

    public function getRuleRequiredWithAll(string $rule, array $presentFields, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required_with_all:' . implode(',', $presentFields)], $additionalRules);
    }

    public function getRuleRequiredWithout(string $rule, array $presentFields, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required_without:' . implode(',', $presentFields)], $additionalRules);
    }

    public function getRuleRequiredWithoutAll(string $rule, array $presentFields, array $additionalRules = []): array
    {
        return array_merge($this->rules[$rule], ['required_without_all:' . implode(',', $presentFields)], $additionalRules);
    }
}
