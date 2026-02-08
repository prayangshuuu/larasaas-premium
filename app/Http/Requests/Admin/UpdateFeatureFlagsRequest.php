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
            'social_login_enabled'                  => ['nullable', 'boolean'],
            'impersonation'                         => ['nullable', 'boolean'],
            'allow_username_change'                 => ['nullable', 'boolean'],
            'require_admin_mfa_for_impersonation'   => ['nullable', 'boolean'],
            'subscription_module_enabled'           => ['nullable', 'boolean'],
            'stripe_payment_enabled'                => ['nullable', 'boolean'],
            'stripe_key'                            => ['nullable', 'string', 'max:255'],
            'stripe_secret'                         => ['nullable', 'string', 'max:255'],
            'stripe_webhook_secret'                 => ['nullable', 'string', 'max:255'],
            'support_enabled'                       => ['nullable', 'boolean'],
            'support_auto_reply_enabled'            => ['nullable', 'boolean'],
        ];
    }
}
