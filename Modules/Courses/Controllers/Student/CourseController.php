<?php

namespace Modules\Courses\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {}

    public function index()
    {
        $student = Auth::user();
        $courses = $this->enrollmentService->getStudentEnrollments($student);

        return Inertia::render('Student/Courses/Index', [
            'courses' => $courses,
        ]);
    }
}

