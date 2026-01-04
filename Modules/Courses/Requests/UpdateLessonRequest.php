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
     * CRITICAL: When FormData is used with files, we need to check request()->all() 
     * to ensure all fields are accessible, not just input().
     */
    protected function prepareForValidation(): void
    {
        // CRITICAL: Get all request data first (for FormData compatibility)
        // When FormData is used with files, some fields might not be accessible via input()
        // So we check request()->all() to get all form fields
        $allData = $this->request->all();
        
        // Match StoreLessonRequest behavior - keep data format consistent
        // Convert empty strings to null for nullable fields (same as StoreLessonRequest)
        
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
            // Check both input() and allData for FormData compatibility
            $value = $this->input($field) ?? ($allData[$field] ?? null);
            if ($value === '') {
                $this->merge([$field => null]);
            } elseif ($value !== null && !isset($allData[$field])) {
                // If value exists but not in allData, merge it
                $this->merge([$field => $value]);
            }
        }

        // Ensure title is always present and trimmed (for FormData compatibility)
        $title = $this->input('title') ?? ($allData['title'] ?? null);
        if ($title !== null && $title !== '') {
            $this->merge(['title' => is_string($title) ? trim($title) : $title]);
        } else {
            // Always merge title, even if empty, to ensure it's present for validation
            $this->merge(['title' => '']);
        }

        // Ensure type is always present (for FormData compatibility)
        $type = $this->input('type') ?? ($allData['type'] ?? null);
        if ($type !== null && $type !== '') {
            $this->merge(['type' => is_string($type) ? trim($type) : $type]);
        } else {
            // Always merge type, even if empty, to ensure it's present for validation
            $this->merge(['type' => '']);
        }

        // CRITICAL: Convert section_id to integer if it's a string
        // Check both input() and allData for FormData compatibility
        // When FormData is used with files, section_id might be in allData but not accessible via input()
        $sectionId = $this->input('section_id') ?? ($allData['section_id'] ?? null);
        
        if ($sectionId !== null && $sectionId !== '') {
            if (is_string($sectionId) && trim($sectionId) !== '') {
                $this->merge(['section_id' => (int) trim($sectionId)]);
            } elseif (is_numeric($sectionId)) {
                $this->merge(['section_id' => (int) $sectionId]);
            } else {
                $this->merge(['section_id' => null]);
            }
        } else {
            // Always merge section_id, even if null/empty, to ensure it's present for validation
            // This is critical for admin users where section_id is required
            $this->merge(['section_id' => null]);
        }

        // Convert order to integer if it's a string (same as StoreLessonRequest)
        // Check both input() and allData for FormData compatibility
        $order = $this->input('order') ?? ($allData['order'] ?? null);
        if ($order !== null && $order !== '') {
            if (is_string($order) && trim($order) !== '') {
                $this->merge(['order' => (int) trim($order)]);
            } elseif (is_numeric($order)) {
                $this->merge(['order' => (int) $order]);
            } else {
                $this->merge(['order' => null]);
            }
        } else {
            $this->merge(['order' => null]);
        }

        // Convert duration_minutes to integer if it's a string (same as StoreLessonRequest)
        // Check both input() and allData for FormData compatibility
        $duration = $this->input('duration_minutes') ?? ($allData['duration_minutes'] ?? null);
        if ($duration !== null && $duration !== '') {
            if (is_string($duration) && trim($duration) !== '') {
                $this->merge(['duration_minutes' => (int) trim($duration)]);
            } elseif (is_numeric($duration)) {
                $this->merge(['duration_minutes' => (int) $duration]);
            } else {
                $this->merge(['duration_minutes' => null]);
            }
        } else {
            $this->merge(['duration_minutes' => null]);
        }

        // Convert is_free to boolean if it's a string
        // Check both input() and allData for FormData compatibility
        $isFree = $this->input('is_free') ?? ($allData['is_free'] ?? null);
        if ($isFree !== null) {
            if (is_string($isFree)) {
                $this->merge(['is_free' => filter_var($isFree, FILTER_VALIDATE_BOOLEAN)]);
            } elseif ($isFree === '') {
                $this->merge(['is_free' => null]);
            } else {
                $this->merge(['is_free' => (bool) $isFree]);
            }
        }
    }

    public function rules(): array
    {
        // CRITICAL: Get type after prepareForValidation has run
        // Check both input() and request()->all() for FormData compatibility
        // When FormData is used with files, some fields might not be accessible via input()
        $allData = $this->request->all();
        $type = $this->input('type', '') ?: ($allData['type'] ?? '');
        $user = $this->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        
        // Title and type are always required - use custom validation to handle both missing and empty values
        $rules = [
            'title' => [
                function ($attribute, $value, $fail) {
                    // Use the value parameter if available, otherwise check input
                    $title = $value !== null ? $value : $this->input('title', '');
                    if (empty($title) || (is_string($title) && trim($title) === '')) {
                        $fail(__('The lesson title is required.'));
                    }
                },
                'string',
                'max:255',
            ],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'type' => [
                function ($attribute, $value, $fail) {
                    // Use the value parameter if available, otherwise check input
                    $type = $value !== null ? $value : $this->input('type', '');
                    if (empty($type) || (is_string($type) && trim($type) === '')) {
                        $fail(__('Please select a lesson type.'));
                    }
                },
                'string',
                'in:text,google_drive_video,video_file,youtube_video,image,document_file,embed_frame,assignment,test,live',
            ],
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
            // CRITICAL: Check both input() and request()->all() for FormData compatibility
            $allData = $this->request->all();
            $type = $this->input('type') ?: ($allData['type'] ?? '');
            $user = $this->user();
            $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
            
            // Note: section_id validation is handled by the 'required' rule in rules() method
            // This custom validation is only needed if we need additional checks beyond required/integer/exists
            // The standard Laravel validation rules should handle it correctly
            
            // Validate live lesson requirements
            if ($type === 'live') {
                $meetingDate = $this->input('live_meeting_date') ?: ($allData['live_meeting_date'] ?? null);
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
                $videoUrl = $this->input('video_url') ?: ($allData['video_url'] ?? null);
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
