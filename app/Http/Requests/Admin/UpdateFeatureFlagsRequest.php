<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeatureFlagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'impersonation'                         => ['nullable', 'boolean'],
            'allow_username_change'                 => ['nullable', 'boolean'],
            'require_admin_mfa_for_impersonation'   => ['nullable', 'boolean'],
        ];
    }
}
