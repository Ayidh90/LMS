<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'duration_hours' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url'],
            'is_published' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('The course title is required.'),
            'level.required' => __('Please select a course level.'),
            'level.in' => __('Invalid course level selected.'),
            'duration_hours.required' => __('The duration is required.'),
            'thumbnail.image' => __('The thumbnail must be an image.'),
            'thumbnail.max' => __('The thumbnail must not exceed 2MB.'),
        ];
    }
}
