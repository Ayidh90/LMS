<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Transform empty strings to null and convert types.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for nullable fields
        $nullableFields = [
            'title_ar',
            'description',
            'description_ar',
            'content',
            'content_ar',
            'video_url',
            'live_meeting_date',
            'live_meeting_link',
        ];

        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        // Convert section_id to integer if it's a string
        if ($this->has('section_id') && $this->input('section_id') !== null && $this->input('section_id') !== '') {
            $sectionId = $this->input('section_id');
            if (is_string($sectionId)) {
                $this->merge(['section_id' => (int) $sectionId]);
            }
        } elseif ($this->has('section_id') && ($this->input('section_id') === '' || $this->input('section_id') === null)) {
            $this->merge(['section_id' => null]);
        }

        // Convert order to integer if it's a string
        if ($this->has('order') && $this->input('order') !== null && $this->input('order') !== '') {
            $order = $this->input('order');
            if (is_string($order)) {
                $this->merge(['order' => (int) $order]);
            }
        } elseif ($this->has('order') && ($this->input('order') === '' || $this->input('order') === null)) {
            $this->merge(['order' => null]);
        }

        // Convert duration_minutes to integer if it's a string
        if ($this->has('duration_minutes') && $this->input('duration_minutes') !== null && $this->input('duration_minutes') !== '') {
            $duration = $this->input('duration_minutes');
            if (is_string($duration)) {
                $this->merge(['duration_minutes' => (int) $duration]);
            }
        } elseif ($this->has('duration_minutes') && ($this->input('duration_minutes') === '' || $this->input('duration_minutes') === null)) {
            $this->merge(['duration_minutes' => null]);
        }

        // Convert is_free to boolean if it's a string
        if ($this->has('is_free')) {
            $isFree = $this->input('is_free');
            if (is_string($isFree)) {
                $this->merge(['is_free' => filter_var($isFree, FILTER_VALIDATE_BOOLEAN)]);
            } elseif ($isFree === null || $isFree === '') {
                $this->merge(['is_free' => null]);
            }
        }
    }

    public function rules(): array
    {
        $type = $this->input('type');
        $user = $this->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:text,google_drive_video,video_file,youtube_video,image,document_file,embed_frame,assignment,test,live'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'section_id' => $isAdmin ? ['required', 'integer', 'exists:sections,id'] : ['nullable', 'integer', 'exists:sections,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
            'live_meeting_date' => $type === 'live' ? ['required', 'date'] : ['nullable', 'date'],
            'live_meeting_link' => ['nullable', 'url'],
        ];

        // Add file validation based on type
        if ($type === 'video_file') {
            // Use mimetypes for better WMV support (WMV can have different MIME types)
            // Accept both extension-based (mimes) and MIME-type-based (mimetypes) validation
            $rules['video_file'] = [
                'nullable', 
                'file',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }
                    
                    $allowedExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
                    $allowedMimeTypes = [
                        'video/mp4',
                        'video/x-msvideo', // AVI
                        'video/quicktime', // MOV
                        'video/x-ms-wmv', // WMV
                        'video/x-flv', // FLV
                        'video/webm', // WEBM
                        'video/x-ms-asf', // ASF/WMV container
                        'application/vnd.ms-asf', // ASF/WMV container
                    ];
                    
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();
                    
                    if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimeTypes)) {
                        $fail(__('validation.mimes', [
                            'attribute' => __('lessons.fields.video_file') ?? 'video file',
                            'values' => implode(', ', $allowedExtensions)
                        ]));
                    }
                },
                'max:102400'
            ]; // 100MB max
            // For file types, video_url can be a file path (e.g., 'lessons/videos/file.mp4') or a URL
            // When updating, the existing file path is sent back, so we allow strings (not just URLs)
            $rules['video_url'] = ['nullable', 'string', 'max:500'];
        } elseif ($type === 'image') {
            $rules['image_file'] = ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:10240']; // 10MB max
            // For file types, video_url can be a file path (e.g., 'lessons/images/file.jpg') or a URL
            $rules['video_url'] = ['nullable', 'string', 'max:500'];
        } elseif ($type === 'document_file') {
            $rules['document_file'] = ['nullable', 'file', 'mimes:pdf,doc,docx,txt,rtf,odt', 'max:10240']; // 10MB max
            // For file types, video_url can be a file path (e.g., 'lessons/documents/file.pdf') or a URL
            $rules['video_url'] = ['nullable', 'string', 'max:500'];
        } elseif (in_array($type, ['youtube_video', 'google_drive_video', 'embed_frame'])) {
            // For URL-based types, video_url must be a valid URL
            $rules['video_url'] = ['nullable', 'url', 'max:500'];
        } else {
            // For other types (text, assignment, test, live), video_url should be null or empty
            // But we allow nullable string to handle edge cases
            $rules['video_url'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $user = $this->user();
            $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
            
            // Validate section_id is required for admin users
            if ($isAdmin) {
                $sectionId = $this->input('section_id');
                if (empty($sectionId) || $sectionId === null || $sectionId === '') {
                    $validator->errors()->add('section_id', __('The section field is required for admin users.'));
                }
            }
            
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
            
            // For file types, when updating, we allow either:
            // 1. A new file upload, OR
            // 2. An existing video_url (from previous upload - can be a file path or URL)
            // We don't require both because the existing file path is stored in video_url
            // For updates, if no new file is uploaded, the existing video_url should be preserved
            // The backend controller will handle preserving the old video_url if needed
            
            // Validate URL-based types have video_url
            if (in_array($type, ['youtube_video', 'google_drive_video', 'embed_frame'])) {
                $videoUrl = $this->input('video_url');
                if (empty($videoUrl) || (is_string($videoUrl) && trim($videoUrl) === '')) {
                    $validator->errors()->add('video_url', __('The video URL is required for this lesson type.'));
                } elseif (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add('video_url', __('The video URL must be a valid URL.'));
                }
            }
            
            // For file types when updating, we don't require a new file if video_url is provided
            // The video_url can be a file path (e.g., 'lessons/videos/file.mp4') or a URL
            // The backend controller will handle preserving existing files
        });
    }

    public function messages(): array
    {
        $user = $this->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        
        $messages = [
            'title.required' => __('The lesson title is required.'),
            'type.required' => __('Please select a lesson type.'),
            'type.in' => __('Invalid lesson type selected.'),
            'order.integer' => __('The lesson order must be a number.'),
            'order.min' => __('The lesson order cannot be negative.'),
            'duration_minutes.integer' => __('The duration must be a number.'),
            'duration_minutes.min' => __('The duration cannot be negative.'),
            'video_url.url' => __('The video URL must be a valid URL.'),
            'video_url.string' => __('The video URL must be a valid string.'),
            'video_url.max' => __('The video URL must not exceed :max characters.'),
            'section_id.exists' => __('The selected section does not exist.'),
            'live_meeting_date.required' => __('lessons.validation.live_meeting_date_required'),
            'live_meeting_date.date' => __('lessons.validation.live_meeting_date_invalid'),
            'live_meeting_link.url' => __('lessons.validation.live_meeting_link_invalid'),
        ];
        
        if ($isAdmin) {
            $messages['section_id.required'] = __('The section field is required for admin users.');
        }
        
        return $messages;
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
