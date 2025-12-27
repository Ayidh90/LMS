<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'national_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'national_id')->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'], // Single role (for backward compatibility)
            'role_ids' => ['nullable', 'array', 'min:1'], // Multiple roles - at least one required
            'role_ids.*' => ['exists:roles,id'], // Each role must exist
            'role' => ['nullable', 'string'], // Keep for backward compatibility
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ensure at least one role is selected (either role_id or role_ids)
            if (empty($this->role_ids) && empty($this->role_id)) {
                $validator->errors()->add('role_ids', __('Please select at least one role.'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => __('The name is required.'),
            'email.required' => __('The email is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('This email is already registered.'),
            'national_id.unique' => __('This national ID is already registered.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'role.required' => __('Please select a user role.'),
            'role.in' => __('Invalid role selected.'),
        ];
    }
}
