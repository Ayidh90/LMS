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
            'student_ids' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Get batch from route
                    $batch = $this->route('batch');
                    
                    if ($batch && $batch->max_students !== null) {
                        $currentStudents = \App\Models\Enrollment::where('batch_id', $batch->id)->count();
                        $newStudentsCount = count($value);
                        $wouldBeTotal = $currentStudents + $newStudentsCount;
                        
                        if ($wouldBeTotal > $batch->max_students) {
                            $fail(__('admin.max_students_would_exceed', [
                                'count' => $newStudentsCount,
                                'max' => $batch->max_students,
                                'current' => $currentStudents,
                                'would_be' => $wouldBeTotal,
                            ]));
                        }
                    }
                },
            ],
            'student_ids.*' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !$user->isStudent() || !$user->is_active) {
                        $fail(__('One or more selected users do not have student role or are inactive.'));
                    }
                },
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
