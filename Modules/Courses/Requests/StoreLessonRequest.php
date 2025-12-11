<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:text,google_drive_video,video_file,youtube_video,image,document_file,embed_frame,assignment,test'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
        ];

        // Add file validation based on type
        $type = $this->input('type');
        if ($type === 'video_file') {
            $rules['video_file'] = ['nullable', 'file', 'mimes:mp4,avi,mov,wmv,flv,webm', 'max:102400']; // 100MB max
            $rules['video_url'] = ['nullable', 'string']; // Can be URL or file path
        } elseif ($type === 'image') {
            $rules['image_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:10240']; // 10MB max
            $rules['video_url'] = ['nullable', 'string']; // Can be URL or file path
        } elseif ($type === 'document_file') {
            $rules['document_file'] = ['nullable', 'file', 'mimes:pdf,doc,docx,txt,rtf,odt', 'max:10240']; // 10MB max
            $rules['video_url'] = ['nullable', 'string']; // Can be URL or file path
        } else {
            $rules['video_url'] = ['nullable', 'url'];
        }

        return $rules;
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            
            // Validate that at least file or URL is provided for file types
            if (in_array($type, ['video_file', 'image', 'document_file'])) {
                $hasFile = $this->hasFile('video_file') || $this->hasFile('image_file') || $this->hasFile('document_file');
                $hasUrl = !empty($this->input('video_url'));
                
                if (!$hasFile && !$hasUrl) {
                    $fieldName = $type === 'image' 
                        ? __('Image file or URL')
                        : ($type === 'document_file' 
                            ? __('Document file or URL')
                            : __('Video file or URL'));
                    $validator->errors()->add('video_url', __('Please provide :field.', ['field' => $fieldName]));
                }
            }
            
            // Validate URL format for video types that require URL
            if (in_array($type, ['youtube_video', 'google_drive_video', 'embed_frame']) && empty($this->input('video_url'))) {
                $validator->errors()->add('video_url', __('The :field is required.', ['field' => __('Video URL')]));
            }
        });
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
