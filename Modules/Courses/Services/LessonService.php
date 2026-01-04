<?php

namespace Modules\Courses\Services;

use Modules\Courses\Repositories\LessonRepository;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Course;
use App\Models\Batch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class LessonService
{
    public function __construct(
        private LessonRepository $repository,
        private LiveMeetingService $liveMeetingService
    ) {}

    public function getByCourse(int $courseId, array $relations = []): Collection
    {
        return $this->repository->getByCourse($courseId, $relations);
    }

    public function getBySection(int $sectionId, array $relations = []): Collection
    {
        return $this->repository->getBySection($sectionId, $relations);
    }

    public function getByType(int $courseId, string $type, array $relations = []): Collection
    {
        return $this->repository->getByType($courseId, $type, $relations);
    }

    public function getAssignments(int $courseId): Collection
    {
        return $this->getByType($courseId, 'assignment', ['course']);
    }

    public function getTests(int $courseId): Collection
    {
        return $this->getByType($courseId, 'test', ['course', 'questions']);
    }

    /**
     * Get tests for a student based on their enrolled courses
     */
    public function getTestsForStudent(array $courseIds): Collection
    {
        return Lesson::whereIn('course_id', $courseIds)
            ->where('type', 'test')
            ->with(['course', 'questions'])
            ->orderBy('order')
            ->get();
    }

    /**
     * Validate that a lesson is of type test
     */
    public function validateTestType(Lesson $lesson): void
    {
        if ($lesson->type !== 'test') {
            abort(404, __('This is not a test.'));
        }
    }

    public function getLessonById(int $id, array $relations = ['questions.answers']): ?Lesson
    {
        return $this->repository->findById($id, $relations);
    }

    public function create(Course $course, array $data): Lesson
    {
        $data['course_id'] = $course->id;
        $data['order'] = $data['order'] ?? $this->repository->getNextOrder($course->id, $data['section_id'] ?? null);
        
        // Generate live meeting link if type is 'live' and link is not provided
        // Pass course to enable waiting room and instructor moderation
        if (isset($data['type']) && $data['type'] === 'live' && empty($data['live_meeting_link'])) {
            $data['live_meeting_link'] = $this->liveMeetingService->generateMeetingLink('jitsi', $course);
        }
        
        return $this->repository->create($data);
    }

    public function update(Lesson $lesson, array $data): bool
    {
        // Generate live meeting link if type is 'live' and link is not provided
        // Pass course to enable waiting room and instructor moderation
        if (isset($data['type']) && $data['type'] === 'live' && empty($data['live_meeting_link']) && empty($lesson->live_meeting_link)) {
            $course = $lesson->course ?? \Modules\Courses\Models\Course::find($lesson->course_id);
            $data['live_meeting_link'] = $this->liveMeetingService->generateMeetingLink('jitsi', $course);
        }
        
        // Handle live meeting fields
        $this->handleLiveMeetingFields($data, $data['type'] ?? $lesson->type);
        
        return $this->repository->update($lesson, $data);
    }

    /**
     * Handle file uploads for lesson creation
     */
    public function handleFileUploads(Request $request, string $lessonType): ?string
    {
        $imageService = app(\App\Services\ImageService::class);
        
        if ($request->hasFile('video_file') && $lessonType === 'video_file') {
            $filePath = $imageService->upload($request->file('video_file'), 'lessons/videos');
            return $filePath ?: null;
        } elseif ($request->hasFile('image_file') && $lessonType === 'image') {
            $filePath = $imageService->upload($request->file('image_file'), 'lessons/images');
            return $filePath ?: null;
        } elseif ($request->hasFile('document_file') && $lessonType === 'document_file') {
            $filePath = $imageService->upload($request->file('document_file'), 'lessons/documents');
            return $filePath ?: null;
        }
        
        return null;
    }

    /**
     * Handle file uploads for lesson update
     */
    public function handleFileUploadsForUpdate(Request $request, Lesson $lesson, array &$data, string $lessonType): void
    {
        $imageService = app(\App\Services\ImageService::class);
        $oldVideoUrl = $lesson->video_url;
        
        if ($request->hasFile('video_file') && $lessonType === 'video_file') {
            $filePath = $imageService->upload($request->file('video_file'), 'lessons/videos', $oldVideoUrl);
            $data['video_url'] = $filePath ?: $oldVideoUrl;
        } elseif ($request->hasFile('image_file') && $lessonType === 'image') {
            $filePath = $imageService->upload($request->file('image_file'), 'lessons/images', $oldVideoUrl);
            $data['video_url'] = $filePath ?: $oldVideoUrl;
        } elseif ($request->hasFile('document_file') && $lessonType === 'document_file') {
            $filePath = $imageService->upload($request->file('document_file'), 'lessons/documents', $oldVideoUrl);
            $data['video_url'] = $filePath ?: $oldVideoUrl;
        } else {
            $this->handleVideoUrlForUpdate($data, $lessonType, $oldVideoUrl);
        }
    }

    /**
     * Handle video_url for update when no file is uploaded
     */
    private function handleVideoUrlForUpdate(array &$data, string $lessonType, ?string $oldVideoUrl): void
    {
        if (in_array($lessonType, ['video_file', 'image', 'document_file'])) {
            if (!isset($data['video_url']) || $data['video_url'] === null || $data['video_url'] === '') {
                $data['video_url'] = $oldVideoUrl ?: null;
            } else {
                $videoUrl = $data['video_url'];
                if (filter_var($videoUrl, FILTER_VALIDATE_URL) && str_contains($videoUrl, 'videos.serve')) {
                    $parsedUrl = parse_url($videoUrl);
                    if (isset($parsedUrl['query'])) {
                        parse_str($parsedUrl['query'], $queryParams);
                        $data['video_url'] = $queryParams['path'] ?? ($oldVideoUrl ?: null);
                    } else {
                        $data['video_url'] = $oldVideoUrl ?: null;
                    }
                }
            }
        } elseif (in_array($lessonType, ['youtube_video', 'google_drive_video', 'embed_frame'])) {
            $data['video_url'] = $data['video_url'] ?? null;
        } else {
            $data['video_url'] = null;
        }
    }

    /**
     * Handle live meeting fields
     */
    private function handleLiveMeetingFields(array &$data, string $lessonType): void
    {
        if ($lessonType === 'live') {
            $data['live_meeting_date'] = $data['live_meeting_date'] ?? null;
            $data['live_meeting_link'] = $data['live_meeting_link'] ?? null;
        } else {
            $data['live_meeting_date'] = null;
            $data['live_meeting_link'] = null;
        }
    }

    /**
     * Get all live meetings
     */
    public function getAllLiveMeetings(): \Illuminate\Support\Collection
    {
        $liveLessons = Lesson::where('type', 'live')
            ->with(['course', 'section'])
            ->orderByRaw('CASE WHEN live_meeting_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('live_meeting_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $liveLessons->map(function ($lesson) {
            try {
                $dateString = $lesson->live_meeting_date 
                    ? $lesson->live_meeting_date->format('Y-m-d H:i:s')
                    : null;

                return [
                    'id' => $lesson->id,
                    'title' => $lesson->translated_title ?? $lesson->title ?? 'Untitled Lesson',
                    'description' => $lesson->translated_description ?? $lesson->description ?? null,
                    'live_meeting_date' => $dateString,
                    'live_meeting_link' => $lesson->live_meeting_link,
                    'order' => $lesson->order ?? 0,
                    'is_free' => $lesson->is_free ?? false,
                    'course' => $lesson->course ? [
                        'id' => $lesson->course->id,
                        'title' => $lesson->course->translated_title ?? $lesson->course->title ?? 'Unknown Course',
                        'slug' => $lesson->course->slug ?? null,
                    ] : null,
                    'section' => $lesson->section ? [
                        'id' => $lesson->section->id,
                        'title' => $lesson->section->translated_title ?? $lesson->section->title ?? 'Unknown Section',
                        'order' => $lesson->section->order ?? 0,
                    ] : null,
                    'duration_minutes' => $lesson->duration_minutes ?? 60,
                ];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error mapping lesson: ' . $e->getMessage(), [
                    'lesson_id' => $lesson->id ?? null
                ]);
                return null;
            }
        })->filter();
    }

    public function delete(Lesson $lesson): bool
    {
        return $this->repository->delete($lesson);
    }

    public function reorder(int $courseId, array $orderedIds): void
    {
        $this->repository->reorder($courseId, $orderedIds);
    }

    public function countByCourse(int $courseId): int
    {
        return $this->repository->countByCourse($courseId);
    }

    public function belongsToCourse(Lesson $lesson, Course $course): bool
    {
        return $lesson->course_id === $course->id;
    }

    public function formatForFrontend(Lesson $lesson, $userAnswers = null, $isInstructor = false): array
    {
        $formatted = [
            'id' => $lesson->id,
            'title' => $lesson->translated_title ?? $lesson->title,
            'title_ar' => $lesson->title_ar,
            'type' => $lesson->type ?? 'video',
            'description' => $lesson->translated_description ?? $lesson->description,
            'description_ar' => $lesson->description_ar,
            'content' => $lesson->translated_content ?? $lesson->content,
            'content_ar' => $lesson->content_ar,
            'video_url' => $this->formatVideoUrl($lesson->video_url),
            'live_meeting_date' => $lesson->live_meeting_date,
            'live_meeting_link' => $lesson->live_meeting_link,
            'is_live_started' => $lesson->is_live_started ?? false,
            'order' => $lesson->order ?? 0,
            'duration_minutes' => $lesson->duration_minutes ?? 0,
            'is_free' => $lesson->is_free ?? false,
            'section_id' => $lesson->section_id,
            'questions' => $lesson->questions ? $lesson->questions->map(fn($q) => $this->formatQuestion($q, $userAnswers)) : [],
        ];
        
        // Automatically generate appropriate link based on user role
        if ($lesson->type === 'live' && $lesson->live_meeting_link) {
            $course = $lesson->course ?? \Modules\Courses\Models\Course::find($lesson->course_id);
            if ($course) {
                if ($isInstructor) {
                    // Get current user to use their email in the link
                    $currentUser = \Illuminate\Support\Facades\Auth::user();
                    
                    // Instructor gets moderator link with current user's email
                    $instructorLink = $this->liveMeetingService->getInstructorLink(
                        $lesson->live_meeting_link,
                        $course,
                        $currentUser instanceof \App\Models\User ? $currentUser : null
                    );
                    $formatted['live_meeting_link_instructor'] = $instructorLink;
                    // Also set as main link for instructor
                    $formatted['live_meeting_link'] = $instructorLink;
                } else {
                    // Students get student link (without login option)
                    $studentLink = $this->liveMeetingService->getStudentLink($lesson->live_meeting_link);
                    $formatted['live_meeting_link'] = $studentLink;
                }
            }
        }
        
        return $formatted;
    }

    /**
     * Format video URL - convert relative paths to full URLs
     * This is public so it can be used by controllers that need to format URLs
     *
     * @param string|null $videoUrl
     * @return string|null
     */
    public function formatVideoUrl(?string $videoUrl): ?string
    {
        if (!$videoUrl) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            return $videoUrl;
        }

        // Remove leading slash if present
        $videoUrl = ltrim($videoUrl, '/');

        // Check if it's a storage path (starts with 'storage/')
        // Try asset() first (in case symlink exists), but also provide route fallback
        if (str_starts_with($videoUrl, 'storage/')) {
            // Remove 'storage/' prefix for route
            $cleanPath = substr($videoUrl, 8); // Remove 'storage/' (8 chars)
            return route('videos.serve', ['path' => $cleanPath]);
        }

        // If it starts with 'storage' but not 'storage/', add the slash
        if (str_starts_with($videoUrl, 'storage')) {
            $cleanPath = substr($videoUrl, 7); // Remove 'storage' (7 chars)
            return route('videos.serve', ['path' => $cleanPath]);
        }

        // Check if it's a path like 'lessons/videos/file.mp4' (from ImageService upload)
        // These are stored in storage/app/public, so we serve through VideoController
        if (str_contains($videoUrl, '/') && !str_starts_with($videoUrl, 'http')) {
            // Use route to serve through VideoController for proper MIME types
            return route('videos.serve', ['path' => $videoUrl]);
        }

        // For other relative paths, assume they're in storage
        return route('videos.serve', ['path' => $videoUrl]);
    }

    private function formatQuestion($question, $userAnswers = null): array
    {
        $userAnswer = $userAnswers ? $userAnswers->get($question->id) : null;
        
        return [
            'id' => $question->id,
            'type' => $question->type ?? 'multiple_choice',
            'question' => $question->translated_question ?? $question->question,
            'question_ar' => $question->question_ar,
            'explanation' => $question->translated_explanation ?? $question->explanation,
            'explanation_ar' => $question->explanation_ar,
            'points' => $question->points ?? 1,
            'order' => $question->order ?? 0,
            'answers' => $question->answers ? $question->answers->map(fn($a) => [
                'id' => $a->id,
                'answer' => $a->translated_answer ?? $a->answer,
                'answer_ar' => $a->answer_ar,
                'is_correct' => $a->is_correct ?? false,
                'order' => $a->order ?? 0,
            ]) : [],
            'user_answer' => $userAnswer ? [
                'id' => $userAnswer->id,
                'answer_id' => $userAnswer->answer_id,
                'answer_text' => $userAnswer->answer_text,
                'is_correct' => $userAnswer->is_correct,
                'points_earned' => $userAnswer->points_earned,
            ] : null,
        ];
    }
}
