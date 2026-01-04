<?php

namespace Modules\Courses\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\LessonPresentationService;
use Modules\Courses\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TestController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private LessonService $lessonService,
        private LessonPresentationService $presentationService
    ) {}

    public function index()
    {
        $student = Auth::user();
        $courseIds = $this->enrollmentService->getEnrolledCourseIds($student);
        $batchIds = $this->enrollmentService->getEnrolledBatchIds($student);
        
        $tests = $this->lessonService->getTestsForStudent($courseIds)
            ->map(fn($lesson) => $this->presentationService->formatTest($lesson, $batchIds));

        return Inertia::render('Student/Tests/Index', [
            'tests' => $tests,
        ]);
    }

    public function show(Lesson $lesson)
    {
        $student = Auth::user();
        
        $this->lessonService->validateTestType($lesson);
        
        if (!$this->enrollmentService->hasAccessToLesson($student, $lesson)) {
            abort(403, __('You do not have access to this test.'));
        }

        $lesson->load(['course', 'questions.answers']);

        return Inertia::render('Student/Tests/Show', [
            'test' => $this->lessonService->formatForFrontend($lesson),
            'course' => $this->presentationService->formatCourse($lesson->course),
        ]);
    }
}
