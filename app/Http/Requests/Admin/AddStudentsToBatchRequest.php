<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddStudentsToBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where('role', 'student')->where('is_active', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => __('Please select at least one student.'),
            'student_ids.array' => __('Student IDs must be an array.'),
            'student_ids.min' => __('Please select at least one student.'),
            'student_ids.*.exists' => __('One or more selected students are invalid or inactive.'),
        ];
    }
}
