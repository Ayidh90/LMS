<?php

namespace Modules\Courses\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Models\UserQuestionAnswer;
use Modules\Courses\Models\Question;
use Modules\Courses\Models\Answer;
use Modules\Courses\Models\Lesson;
use App\Models\Batch;
use Modules\Courses\Services\LessonAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionAnswerController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private LessonAttendanceService $attendanceService
    ) {}

    public function store(Request $request, Lesson $lesson, Question $question)
    {
        $student = Auth::user();
        $batchIds = $this->enrollmentService->getEnrolledBatchIds($student);
        
        if (!$this->hasAccess($lesson, $batchIds)) {
            abort(403, __('You do not have access to this lesson.'));
        }

        $validated = $this->validateAnswer($request, $question);
        
        $result = $this->calculateResult($question, $validated);

        DB::transaction(function () use ($student, $question, $validated, $result, $lesson) {
            UserQuestionAnswer::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_id' => $validated['answer_id'] ?? null,
                    'answer_text' => $validated['answer_text'] ?? null,
                    'is_correct' => $result['is_correct'],
                    'points_earned' => $result['points_earned'],
                ]
            );
        });

        // Load course relationship for attendance service
        $lesson->load('course');
        
        // Check if all questions are answered and mark attendance if so
        $this->attendanceService->markAttendanceAfterQuestions($student, $lesson->course, $lesson);

        return back()->with('success', __('Answer submitted successfully.'));
    }

    private function hasAccess(Lesson $lesson, $batchIds): bool
    {
        return Batch::where('course_id', $lesson->course_id)
            ->whereIn('id', $batchIds)
            ->exists();
    }

    private function validateAnswer(Request $request, Question $question): array
    {
        $rules = [];
        $messages = [];

        if (in_array($question->type, ['multiple_choice', 'true_false'])) {
            $rules['answer_id'] = ['required', 'exists:answers,id'];
            $messages['answer_id.required'] = __('Please select an answer.');
        } else {
            $rules['answer_text'] = ['required', 'string', 'max:5000'];
            $messages['answer_text.required'] = __('Please provide an answer.');
        }

        return $request->validate($rules, $messages);
    }

    private function calculateResult(Question $question, array $validated): array
    {
        $isCorrect = false;
        $pointsEarned = 0;

        if (in_array($question->type, ['multiple_choice', 'true_false'])) {
            $selectedAnswer = Answer::find($validated['answer_id']);
            if ($selectedAnswer && $selectedAnswer->is_correct) {
                $isCorrect = true;
                $pointsEarned = $question->points;
            }
        }
        // For short_answer and essay, instructor grades manually

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
        ];
    }
}

