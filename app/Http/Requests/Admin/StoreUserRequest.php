<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:users,national_id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'role' => ['nullable', 'string'], // Keep for backward compatibility, will be set from role_id
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
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
            'role.required' => __('Please select a user role.'),
            'role.in' => __('Invalid role selected.'),
        ];
    }
}
