<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Enrollment;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            
            if (!$user || (!$user->isInstructor() && !$user->hasPermission('dashboard.instructor'))) {
                abort(403, __('messages.forbidden'));
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $instructor = Auth::user();
        
        // Get batches assigned to this instructor
        $batches = Batch::where('instructor_id', $instructor->id)
            ->with(['course', 'enrollments'])
            ->withCount('enrollments')
            ->latest()
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'name' => $batch->translated_name,
                    'course' => [
                        'id' => $batch->course->id,
                        'title' => $batch->course->translated_title,
                        'slug' => $batch->course->slug,
                    ],
                    'enrollments_count' => $batch->enrollments_count,
                    'start_date' => $batch->start_date,
                    'end_date' => $batch->end_date,
                    'is_active' => $batch->is_active,
                ];
            });

        // Get unique courses through batches
        $courses = Course::whereHas('batches', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->distinct()->get();

        $stats = [
            'total_batches' => Batch::where('instructor_id', $instructor->id)->count(),
            'active_batches' => Batch::where('instructor_id', $instructor->id)
                ->where('is_active', true)->count(),
            'total_students' => Enrollment::whereHas('batch', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })->distinct('student_id')->count(),
            'total_enrollments' => Enrollment::whereHas('batch', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })->count(),
            'total_courses' => $courses->count(),
        ];

        return Inertia::render('Instructor/Dashboard', [
            'stats' => $stats,
            'batches' => $batches,
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->translated_title,
                    'slug' => $course->slug,
                ];
            }),
        ]);
    }
}

