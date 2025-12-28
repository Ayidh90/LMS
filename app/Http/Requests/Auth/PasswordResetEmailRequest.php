<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('Please enter your email or national ID.'),
        ];
    }
}

