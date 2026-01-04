<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Services\LessonService;
use App\Models\Batch;

class LessonPresentationService
{
    public function __construct(
        private LessonService $lessonService
    ) {}

    /**
     * Format course for display
     */
    public function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    /**
     * Format lesson for list view
     */
    public function formatLesson(Lesson $lesson): array
    {
        $formatted = $this->lessonService->formatForFrontend($lesson);
        
        return [
            ...$formatted,
            'section' => $lesson->section ? [
                'id' => $lesson->section->id,
                'title' => $lesson->section->translated_title ?? $lesson->section->title,
            ] : null,
            'questions_count' => $lesson->questions ? $lesson->questions->count() : 0,
        ];
    }

    /**
     * Format lesson for show/edit view
     */
    public function formatLessonFull(Lesson $lesson): array
    {
        $formatted = $this->lessonService->formatForFrontend($lesson);
        
        return [
            ...$formatted,
            'translated_title' => $formatted['title'],
            'title' => $lesson->title,
            'translated_description' => $formatted['description'],
            'description' => $lesson->description,
            'translated_content' => $formatted['content'],
            'content' => $lesson->content,
            'translated_video_url' => $formatted['video_url'],
            'video_url' => $lesson->video_url,
            'section' => $lesson->section ? [
                'id' => $lesson->section->id,
                'title' => $lesson->section->translated_title ?? $lesson->section->title,
            ] : null,
            'questions' => $lesson->questions ? $lesson->questions->map(fn($q) => [
                'id' => $q->id,
                'type' => $q->type,
                'question' => $q->translated_question ?? $q->question,
                'points' => $q->points,
                'order' => $q->order,
                'answers_count' => $q->answers ? $q->answers->count() : 0,
            ]) : [],
        ];
    }

    /**
     * Get lesson types for forms
     */
    public function getLessonTypes(): array
    {
        return [
            ['value' => 'text', 'label' => __('lessons.types.text')],
            ['value' => 'youtube_video', 'label' => __('lessons.types.youtube_video')],
            ['value' => 'google_drive_video', 'label' => __('lessons.types.google_drive_video')],
            ['value' => 'video_file', 'label' => __('lessons.types.video_file')],
            ['value' => 'image', 'label' => __('lessons.types.image')],
            ['value' => 'document_file', 'label' => __('lessons.types.document_file')],
            ['value' => 'embed_frame', 'label' => __('lessons.types.embed_frame')],
            ['value' => 'assignment', 'label' => __('lessons.types.assignment')],
            ['value' => 'test', 'label' => __('lessons.types.test')],
            ['value' => 'live', 'label' => __('lessons.types.live')],
        ];
    }

    /**
     * Get question types for forms
     */
    public function getQuestionTypes(): array
    {
        return [
            ['value' => 'multiple_choice', 'label' => __('Multiple Choice')],
            ['value' => 'true_false', 'label' => __('True/False')],
            ['value' => 'short_answer', 'label' => __('Short Answer')],
            ['value' => 'essay', 'label' => __('Essay')],
        ];
    }

    /**
     * Format test for student list view
     */
    public function formatTest(Lesson $lesson, array $batchIds): array
    {
        $batch = Batch::where('course_id', $lesson->course_id)
            ->whereIn('id', $batchIds)
            ->first();
        
        return [
            'id' => $lesson->id,
            'title' => $lesson->translated_title,
            'description' => $lesson->translated_description,
            'order' => $lesson->order,
            'questions_count' => $lesson->questions->count(),
            'course' => [
                'id' => $lesson->course->id,
                'title' => $lesson->course->translated_title,
                'slug' => $lesson->course->slug,
            ],
            'batch' => $batch ? [
                'id' => $batch->id,
                'name' => $batch->translated_name,
            ] : null,
            'created_at' => $lesson->created_at,
        ];
    }
}

