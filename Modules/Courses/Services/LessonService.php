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
        if (isset($data['type']) && $data['type'] === 'live' && empty($data['live_meeting_link'])) {
            $data['live_meeting_link'] = $this->liveMeetingService->generateMeetingLink('jitsi');
        }
        
        return $this->repository->create($data);
    }

    public function update(Lesson $lesson, array $data): bool
    {
        // Generate live meeting link if type is 'live' and link is not provided
        if (isset($data['type']) && $data['type'] === 'live' && empty($data['live_meeting_link']) && empty($lesson->live_meeting_link)) {
            $data['live_meeting_link'] = $this->liveMeetingService->generateMeetingLink('jitsi');
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

    public function formatForFrontend(Lesson $lesson, $userAnswers = null): array
    {
        return [
            'id' => $lesson->id,
            'title' => $lesson->translated_title ?? $lesson->title,
            'title_ar' => $lesson->title_ar,
            'type' => $lesson->type ?? 'video',
            'description' => $lesson->translated_description ?? $lesson->description,
            'description_ar' => $lesson->description_ar,
            'content' => $lesson->translated_content ?? $lesson->content,
            'content_ar' => $lesson->content_ar,
            'video_url' => $lesson->video_url,
            'live_meeting_date' => $lesson->live_meeting_date,
            'live_meeting_link' => $lesson->live_meeting_link,
            'order' => $lesson->order ?? 0,
            'duration_minutes' => $lesson->duration_minutes ?? 0,
            'is_free' => $lesson->is_free ?? false,
            'section_id' => $lesson->section_id,
            'questions' => $lesson->questions ? $lesson->questions->map(fn($q) => $this->formatQuestion($q, $userAnswers)) : [],
        ];
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
