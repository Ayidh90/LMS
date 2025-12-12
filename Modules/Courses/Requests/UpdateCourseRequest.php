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
            'title.string' => __('The course title must be a valid string.'),
            'title.max' => __('The course title must not exceed 255 characters.'),
            'title_ar.max' => __('The Arabic title must not exceed 255 characters.'),
            'level.required' => __('Please select a course level.'),
            'level.in' => __('Invalid course level selected. Please choose beginner, intermediate, or advanced.'),
            'duration_hours.required' => __('The duration is required.'),
            'duration_hours.integer' => __('The duration must be a whole number.'),
            'duration_hours.min' => __('The duration must be at least 0 hours.'),
            'thumbnail.image' => __('The thumbnail must be an image file.'),
            'thumbnail.max' => __('The thumbnail must not exceed 2MB.'),
            'thumbnail_url.url' => __('The thumbnail URL must be a valid URL.'),
            'is_published.boolean' => __('The publish status must be true or false.'),
        ];
    }
}
