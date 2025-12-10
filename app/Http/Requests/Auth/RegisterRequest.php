<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:users,national_id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('The name is required.'),
            'email.required' => __('The email is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('This email is already registered.'),
            'national_id.unique' => __('This national ID is already registered.'),
            'password.required' => __('The password is required.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }
}
