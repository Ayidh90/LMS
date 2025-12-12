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
        $type = $this->input('type');
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:text,google_drive_video,video_file,youtube_video,image,document_file,embed_frame,assignment,test,live'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
            'live_meeting_date' => $type === 'live' ? ['required', 'date'] : ['nullable', 'date'],
            'live_meeting_link' => ['nullable', 'url'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            
            // Validate live lesson requirements
            if ($type === 'live') {
                $meetingDate = $this->input('live_meeting_date');
                // Check if date is empty, null, or just whitespace
                if (empty($meetingDate) || $meetingDate === null || (is_string($meetingDate) && trim($meetingDate) === '')) {
                    $validator->errors()->add('live_meeting_date', __('lessons.validation.live_meeting_date_required'));
                } elseif (!$this->isValidDateTime($meetingDate)) {
                    $validator->errors()->add('live_meeting_date', __('lessons.validation.live_meeting_date_invalid'));
                }
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
            'live_meeting_date.required' => __('lessons.validation.live_meeting_date_required'),
            'live_meeting_date.date' => __('lessons.validation.live_meeting_date_invalid'),
            'live_meeting_link.url' => __('lessons.validation.live_meeting_link_invalid'),
        ];
    }
    
    /**
     * Check if the datetime string is valid
     */
    private function isValidDateTime(?string $dateTime): bool
    {
        if (empty($dateTime)) {
            return false;
        }
        
        // Check if it's in datetime-local format (YYYY-MM-DDTHH:mm)
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $dateTime)) {
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateTime);
            return $date !== false;
        }
        
        // Also accept standard date formats
        try {
            new \DateTime($dateTime);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
