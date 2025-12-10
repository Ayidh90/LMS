<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Modules\Courses\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index()
    {
        $student = Auth::user();

        $stats = [
            'enrolled_courses' => Enrollment::where('student_id', $student->id)->count(),
            'completed_courses' => Enrollment::where('student_id', $student->id)
                ->where('status', 'completed')->count(),
            'in_progress' => Enrollment::where('student_id', $student->id)
                ->where('status', 'enrolled')->count(),
        ];

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch.instructor']) // Load through batches
            ->latest()
            ->get();

        // Get courses through batches - check if student is enrolled in any batch of the course
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->join('batches', 'enrollments.batch_id', '=', 'batches.id')
            ->pluck('batches.course_id')
            ->unique();

        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Student/Dashboard', [
            'stats' => $stats,
            'enrollments' => $enrollments,
            'availableCourses' => $availableCourses,
        ]);
    }
}

