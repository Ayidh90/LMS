<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
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
            'type' => ['required', 'in:text,google_drive_video,video_file,youtube_video,image,document_file,embed_frame,assignment,test'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('The lesson title is required.'),
            'type.required' => __('Please select a lesson type.'),
            'type.in' => __('Invalid lesson type selected.'),
            'order.integer' => __('The lesson order must be a number.'),
            'order.min' => __('The lesson order cannot be negative.'),
            'duration_minutes.integer' => __('The duration must be a number.'),
            'duration_minutes.min' => __('The duration cannot be negative.'),
            'video_url.url' => __('The video URL must be a valid URL.'),
            'section_id.exists' => __('The selected section does not exist.'),
        ];
    }
}
