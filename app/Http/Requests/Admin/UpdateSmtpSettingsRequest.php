<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmtpSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'host'       => ['required', 'string', 'max:191'],
            'port'       => ['required', 'integer', 'min:1', 'max:65535'],
            'username'   => ['nullable', 'string', 'max:191'],
            'password'   => ['nullable', 'string', 'max:191'],
            'encryption' => ['nullable', 'in:tls,ssl'],
            'from_name'  => ['nullable', 'string', 'max:191'],
            'from_addr'  => ['nullable', 'email', 'max:191'],
        ];
    }
}
