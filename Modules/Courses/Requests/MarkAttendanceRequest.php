<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.batch_id' => 'required|exists:batches,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string|max:1000',
            'attendances.*.attended_at' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attendances.required' => __('At least one attendance record is required.'),
            'attendances.array' => __('Attendances must be an array.'),
            'attendances.*.student_id.required' => __('Student ID is required for each attendance record.'),
            'attendances.*.student_id.exists' => __('One or more selected students do not exist.'),
            'attendances.*.batch_id.required' => __('Batch ID is required for each attendance record.'),
            'attendances.*.batch_id.exists' => __('One or more selected batches do not exist.'),
            'attendances.*.status.required' => __('Status is required for each attendance record.'),
            'attendances.*.status.in' => __('Status must be one of: present, absent, late, or excused.'),
            'attendances.*.notes.max' => __('Notes cannot exceed 1000 characters.'),
            'attendances.*.attended_at.date' => __('Attended at must be a valid date.'),
        ];
    }
}

