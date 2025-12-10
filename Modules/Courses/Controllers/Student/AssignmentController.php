<?php

namespace Modules\Courses\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Models\Lesson;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private LessonService $lessonService
    ) {}

    public function index()
    {
        $student = Auth::user();
        $courseIds = $this->enrollmentService->getEnrolledCourseIds($student);
        $batchIds = $this->enrollmentService->getEnrolledBatchIds($student);
        
        $assignments = Lesson::whereIn('course_id', $courseIds)
            ->where('type', 'assignment')
            ->with(['course'])
            ->orderBy('order')
            ->get()
            ->map(fn($lesson) => $this->formatAssignment($lesson, $batchIds));

        return Inertia::render('Student/Assignments/Index', [
            'assignments' => $assignments,
        ]);
    }

    public function show(Lesson $lesson)
    {
        $student = Auth::user();
        
        if ($lesson->type !== 'assignment') {
            abort(404);
        }
        
        $batchIds = $this->enrollmentService->getEnrolledBatchIds($student);
        
        if (!$this->hasAccess($lesson, $batchIds)) {
            abort(403, __('You do not have access to this assignment.'));
        }

        $lesson->load('course');

        return Inertia::render('Student/Assignments/Show', [
            'assignment' => $this->lessonService->formatForFrontend($lesson),
            'course' => [
                'id' => $lesson->course->id,
                'title' => $lesson->course->translated_title,
                'slug' => $lesson->course->slug,
            ],
        ]);
    }

    private function hasAccess(Lesson $lesson, $batchIds): bool
    {
        return Batch::where('course_id', $lesson->course_id)
            ->whereIn('id', $batchIds)
            ->exists();
    }

    private function formatAssignment(Lesson $lesson, $batchIds): array
    {
        $batch = Batch::where('course_id', $lesson->course_id)
            ->whereIn('id', $batchIds)
            ->first();
        
        return [
            'id' => $lesson->id,
            'title' => $lesson->translated_title,
            'description' => $lesson->translated_description,
            'order' => $lesson->order,
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

