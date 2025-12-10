<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreQuestionRequest;
use Modules\Courses\Services\QuestionService;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Question;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionService $questionService,
        private LessonService $lessonService
    ) {}

    public function index(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $questions = $this->questionService->getByLesson($lesson->id);

        return Inertia::render('Admin/Questions/Index', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'questions' => $questions->map(fn($q) => $this->formatQuestion($q)),
        ]);
    }

    public function create(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        return Inertia::render('Admin/Questions/Create', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'questionTypes' => $this->getQuestionTypes(),
        ]);
    }

    public function store(StoreQuestionRequest $request, Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $data = $request->validated();

        $this->questionService->createWithAnswers([
            'lesson_id' => $lesson->id,
            'type' => $data['type'],
            'question' => $data['question'],
            'question_ar' => $data['question_ar'] ?? null,
            'explanation' => $data['explanation'] ?? null,
            'explanation_ar' => $data['explanation_ar'] ?? null,
            'points' => $data['points'],
            'order' => $data['order'] ?? null,
        ], $data['answers'] ?? []);

        return redirect()->route('admin.courses.lessons.questions.index', [$course, $lesson])
            ->with('success', __('Question created successfully.'));
    }

    public function show(Course $course, Lesson $lesson, Question $question)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        if (!$this->questionService->belongsToLesson($question, $lesson)) {
            abort(404);
        }

        $question->load('answers');

        return Inertia::render('Admin/Questions/Show', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'question' => $this->formatQuestionFull($question),
        ]);
    }

    public function edit(Course $course, Lesson $lesson, Question $question)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        if (!$this->questionService->belongsToLesson($question, $lesson)) {
            abort(404);
        }

        $question->load('answers');

        return Inertia::render('Admin/Questions/Edit', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'question' => $this->formatQuestionFull($question),
            'questionTypes' => $this->getQuestionTypes(),
        ]);
    }

    public function update(StoreQuestionRequest $request, Course $course, Lesson $lesson, Question $question)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        if (!$this->questionService->belongsToLesson($question, $lesson)) {
            abort(404);
        }

        $data = $request->validated();

        $this->questionService->updateWithAnswers($question, [
            'type' => $data['type'],
            'question' => $data['question'],
            'question_ar' => $data['question_ar'] ?? null,
            'explanation' => $data['explanation'] ?? null,
            'explanation_ar' => $data['explanation_ar'] ?? null,
            'points' => $data['points'],
            'order' => $data['order'] ?? $question->order,
        ], $data['answers'] ?? []);

        return redirect()->route('admin.courses.lessons.questions.index', [$course, $lesson])
            ->with('success', __('Question updated successfully.'));
    }

    public function destroy(Course $course, Lesson $lesson, Question $question)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        if (!$this->questionService->belongsToLesson($question, $lesson)) {
            abort(404);
        }

        $this->questionService->delete($question);

        return redirect()->route('admin.courses.lessons.questions.index', [$course, $lesson])
            ->with('success', __('Question deleted successfully.'));
    }

    public function reorder(Request $request, Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $this->questionService->reorder($lesson->id, $validated['question_ids']);

        return back()->with('success', __('Questions reordered successfully.'));
    }

    private function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    private function formatLesson(Lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'title' => $lesson->translated_title ?? $lesson->title,
            'type' => $lesson->type,
        ];
    }

    private function formatQuestion(Question $question): array
    {
        return [
            'id' => $question->id,
            'type' => $question->type,
            'question' => $question->translated_question ?? $question->question,
            'question_ar' => $question->question_ar,
            'points' => $question->points,
            'order' => $question->order,
            'answers_count' => $question->answers ? $question->answers->count() : 0,
        ];
    }

    private function formatQuestionFull(Question $question): array
    {
        return [
            ...$this->formatQuestion($question),
            'explanation' => $question->translated_explanation ?? $question->explanation,
            'explanation_ar' => $question->explanation_ar,
            'answers' => $question->answers ? $question->answers->map(fn($a) => [
                'id' => $a->id,
                'answer' => $a->translated_answer ?? $a->answer,
                'answer_ar' => $a->answer_ar,
                'is_correct' => $a->is_correct,
                'order' => $a->order,
            ]) : [],
        ];
    }

    private function getQuestionTypes(): array
    {
        return [
            ['value' => 'multiple_choice', 'label' => __('Multiple Choice')],
            ['value' => 'true_false', 'label' => __('True/False')],
            ['value' => 'short_answer', 'label' => __('Short Answer')],
            ['value' => 'essay', 'label' => __('Essay')],
        ];
    }
}

