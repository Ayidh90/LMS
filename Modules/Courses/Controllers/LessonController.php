<?php

namespace Modules\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\UserQuestionAnswer;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Requests\StoreLessonRequest;
use Modules\Courses\Requests\UpdateLessonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $this->authorize('view', $course);
        
        $lessons = $course->lessons()->orderBy('order')->get();
        $user = Auth::user();
        // Only admins can edit lessons - instructors cannot
        $canEdit = $user && $user->isAdmin();

        return Inertia::render('Lessons/Index', [
            'course' => $course, // Removed instructor loading - instructors are assigned to batches
            'lessons' => $lessons,
            'canEdit' => $canEdit,
        ]);
    }

    public function create(Course $course)
    {
        // Only admins can create lessons
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.forbidden'));
        }

        return Inertia::render('Lessons/Create', [
            'course' => $course,
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        // Only admins can create lessons
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validated();

        $lesson = $course->lessons()->create([
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'content' => $validated['content'] ?? null,
            'content_ar' => $validated['content_ar'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_free' => $validated['is_free'] ?? false,
        ]);

        return redirect()->route('lessons.show', [$course, $lesson])->with('success', __('messages.lessons.created_successfully'));
    }

    public function show(Course $course, Lesson $lesson)
    {
        $this->authorize('view', $course);
        
        $user = Auth::user();
        
        // If user is an instructor teaching this course, redirect to instructor lesson page
        if ($user && $user->isInstructor()) {
            $hasAccess = Batch::where('course_id', $course->id)
                ->where('instructor_id', $user->id)
                ->exists();
            
            if ($hasAccess) {
                return redirect()->route('instructor.lessons.show', [
                    $course->slug ?? $course->id,
                    $lesson->id
                ]);
            }
        }
        
        // Load lesson with questions and answers
        $lesson->load(['course', 'questions.answers']);

        // Get user answers if authenticated
        $userAnswers = collect();
        if ($user) {
            $userAnswers = UserQuestionAnswer::whereIn('question_id', $lesson->questions->pluck('id'))
                ->where('user_id', $user->id)
                ->with(['answer:id,answer,answer_ar'])
                ->get()
                ->keyBy('question_id');
        }

        // Format lesson data with proper localization
        $formattedLesson = [
            'id' => $lesson->id,
            'title' => $lesson->translated_title ?? $lesson->title,
            'title_ar' => $lesson->title_ar,
            'type' => $lesson->type,
            'description' => $lesson->translated_description ?? $lesson->description,
            'description_ar' => $lesson->description_ar,
            'content' => $lesson->translated_content ?? $lesson->content,
            'content_ar' => $lesson->content_ar,
            'video_url' => $lesson->video_url,
            'live_meeting_date' => $lesson->live_meeting_date,
            'live_meeting_link' => $lesson->live_meeting_link,
            'order' => $lesson->order,
            'duration_minutes' => $lesson->duration_minutes,
            'is_free' => $lesson->is_free,
            'questions' => $lesson->questions->map(function ($question) use ($userAnswers) {
                $userAnswer = $userAnswers->get($question->id);
                
                return [
                    'id' => $question->id,
                    'type' => $question->type,
                    'question' => $question->translated_question ?? $question->question,
                    'question_ar' => $question->question_ar,
                    'explanation' => $question->translated_explanation ?? $question->explanation,
                    'explanation_ar' => $question->explanation_ar,
                    'points' => $question->points,
                    'order' => $question->order,
                    'answers' => $question->answers->map(function ($answer) {
                        return [
                            'id' => $answer->id,
                            'answer' => $answer->translated_answer ?? $answer->answer,
                            'answer_ar' => $answer->answer_ar,
                            'is_correct' => $answer->is_correct,
                            'order' => $answer->order,
                        ];
                    }),
                    'user_answer' => $userAnswer ? [
                        'id' => $userAnswer->id,
                        'answer_id' => $userAnswer->answer_id,
                        'answer_text' => $userAnswer->answer_text,
                        'selected_answer' => $userAnswer->answer ? [
                            'id' => $userAnswer->answer->id,
                            'answer' => $userAnswer->answer->translated_answer ?? $userAnswer->answer->answer,
                        ] : null,
                        'is_correct' => $userAnswer->is_correct,
                        'points_earned' => $userAnswer->points_earned,
                        'submitted_at' => $userAnswer->created_at,
                    ] : null,
                ];
            }),
        ];

        // Format course data with proper localization
        $formattedCourse = [
            'id' => $course->id,
            'title' => $course->translated_title ?? $course->title,
            'slug' => $course->slug,
        ];

        return Inertia::render('Lessons/Show', [
            'course' => $formattedCourse,
            'lesson' => $formattedLesson,
        ]);
    }

    public function edit(Course $course, Lesson $lesson)
    {
        // Only admins can edit lessons
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.forbidden'));
        }

        return Inertia::render('Lessons/Edit', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        // Only admins can update lessons
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validated();

        $lesson->update([
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'content' => $validated['content'] ?? null,
            'content_ar' => $validated['content_ar'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_free' => $validated['is_free'] ?? false,
        ]);

        return redirect()->route('lessons.show', [$course, $lesson])->with('success', __('messages.lessons.updated_successfully'));
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        // Only admins can delete lessons
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.forbidden'));
        }

        $lesson->delete();

        return redirect()->route('lessons.index', $course)->with('success', __('messages.lessons.deleted_successfully'));
    }
}

