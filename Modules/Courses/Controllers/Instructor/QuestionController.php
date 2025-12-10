<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreQuestionRequest;
use Modules\Courses\Services\QuestionService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Question;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function __construct(
        private QuestionService $questionService
    ) {}

    public function index(Course $course, Lesson $lesson)
    {
        Gate::authorize('viewLessons', $course);

        $questions = $this->questionService->getByLesson($lesson->id);

        return Inertia::render('Instructor/Questions/Index', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'questions' => $questions->map(fn($q) => $this->questionService->formatForFrontend($q)),
        ]);
    }

    public function create(Course $course, Lesson $lesson)
    {
        Gate::authorize('createLesson', $course);

        return Inertia::render('Instructor/Questions/Create', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
        ]);
    }

    public function store(StoreQuestionRequest $request, Course $course, Lesson $lesson)
    {
        Gate::authorize('createLesson', $course);

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

        return redirect()->route('instructor.lessons.show', [$course, $lesson])
            ->with('success', __('Question created successfully.'));
    }

    public function show(Course $course, Lesson $lesson, Question $question)
    {
        Gate::authorize('viewLessons', $course);

        $question->load('answers');

        return Inertia::render('Instructor/Questions/Show', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'question' => $this->questionService->formatForFrontend($question),
        ]);
    }

    public function destroy(Course $course, Lesson $lesson, Question $question)
    {
        Gate::authorize('deleteLesson', $course);

        $this->questionService->delete($question);

        return redirect()->route('instructor.lessons.show', [$course, $lesson])
            ->with('success', __('Question deleted successfully.'));
    }

    public function reorder(\Illuminate\Http\Request $request, Course $course, Lesson $lesson)
    {
        Gate::authorize('updateLesson', $course);

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
}
