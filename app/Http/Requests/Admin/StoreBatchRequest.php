<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'instructor_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !$user->isInstructor() || !$user->is_active) {
                        $fail(__('The selected instructor is invalid or inactive.'));
                    }
                },
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('The batch name is required.'),
            'instructor_id.required' => __('Please select an instructor.'),
            'instructor_id.exists' => __('The selected instructor is invalid or inactive.'),
            'end_date.after_or_equal' => __('The end date must be after or equal to the start date.'),
            'max_students.min' => __('Maximum students must be at least 1.'),
        ];
    }
}
