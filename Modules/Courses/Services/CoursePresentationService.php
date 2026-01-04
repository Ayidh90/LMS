<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Course;
use Modules\Courses\Services\LessonService;

class CoursePresentationService
{
    public function __construct(
        private LessonService $lessonService
    ) {}

    /**
     * Load course relations for show page
     */
    public function loadCourseRelations(Course $course): void
    {
        $course->load([
            'sections' => fn($query) => $query->orderBy('order'),
            'sections.lessons' => fn($query) => $query->orderBy('order')->with(['section:id,title,title_ar', 'questions.answers']),
            'lessons' => fn($query) => $query->orderBy('order')->with(['section:id,title,title_ar', 'questions.answers']),
            'batches' => fn($query) => $query->with('instructor:id,name,email'),
        ]);
    }

    /**
     * Make translated fields visible for course and its relations
     */
    public function prepareCourseForDisplay(Course $course): void
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);
        $this->makeSectionsVisible($course);
        $this->makeLessonsVisible($course->lessons);
    }

    /**
     * Make translated fields visible for sections
     */
    private function makeSectionsVisible(Course $course): void
    {
        if (!$course->sections) {
            return;
        }

        foreach ($course->sections as $section) {
            $section->makeVisible(['translated_title', 'translated_description']);

            if ($section->lessons) {
                $this->makeLessonsVisible($section->lessons);
            }
        }
    }

    /**
     * Make translated fields visible for lessons and format video_url
     */
    private function makeLessonsVisible($lessons): void
    {
        if (!$lessons) {
            return;
        }

        foreach ($lessons as $lesson) {
            $lesson->makeVisible(['translated_title', 'translated_description', 'translated_content']);
            
            if ($lesson->video_url) {
                $formattedUrl = $this->lessonService->formatVideoUrl($lesson->video_url);
                $lesson->setAttribute('video_url', $formattedUrl);
            }

            if ($lesson->questions) {
                foreach ($lesson->questions as $question) {
                    $question->makeVisible(['translated_question', 'translated_explanation']);
                }
            }
        }
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
}

