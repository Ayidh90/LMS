<?php

namespace Modules\Courses\Services;

use Modules\Courses\Repositories\LessonRepository;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Course;
use Illuminate\Database\Eloquent\Collection;

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
        
        return $this->repository->update($lesson, $data);
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
                    // Instructor gets moderator link
                    $instructorLink = $this->liveMeetingService->getInstructorLink(
                        $lesson->live_meeting_link,
                        $course
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
