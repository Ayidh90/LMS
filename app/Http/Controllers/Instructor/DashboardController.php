<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\Track;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            /** @var \App\Models\User|null $user */
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
        })->with('track')->distinct()->get();
        
        // Calculate enrollments count for each course
        $courses = $courses->map(function($course) use ($instructor) {
            $batchIds = $course->batches()
                ->where('instructor_id', $instructor->id)
                ->pluck('id')
                ->toArray();
            
            $enrollmentsCount = 0;
            if (!empty($batchIds)) {
                $enrollmentsCount = Enrollment::whereIn('batch_id', $batchIds)->count();
            }
            
            $course->enrollments_count = $enrollmentsCount;
            return $course;
        });

        // Get tracks that have courses assigned to this instructor
        $trackIds = $courses->pluck('track_id')->filter()->unique()->toArray();
        $tracks = Track::whereIn('id', $trackIds)
            ->with(['courses' => function($query) use ($instructor) {
                $query->whereHas('batches', function($q) use ($instructor) {
                    $q->where('instructor_id', $instructor->id);
                });
            }])
            ->get()
            ->map(function($track) use ($instructor) {
                $trackCourses = $track->courses->map(function($course) use ($instructor) {
                    $batchIds = $course->batches()
                        ->where('instructor_id', $instructor->id)
                        ->pluck('id')
                        ->toArray();
                    
                    $enrollmentsCount = 0;
                    if (!empty($batchIds)) {
                        $enrollmentsCount = Enrollment::whereIn('batch_id', $batchIds)->count();
                    }
                    
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'title_ar' => $course->title_ar,
                        'translated_title' => $course->translated_title,
                        'description' => $course->description,
                        'description_ar' => $course->description_ar,
                        'translated_description' => $course->translated_description,
                        'slug' => $course->slug,
                        'is_published' => $course->is_published ?? false,
                        'enrollments_count' => $enrollmentsCount,
                    ];
                });
                
                return [
                    'id' => $track->id,
                    'name' => $track->name,
                    'name_ar' => $track->name_ar,
                    'translated_name' => $track->translated_name,
                    'description' => $track->description,
                    'description_ar' => $track->description_ar,
                    'translated_description' => $track->translated_description,
                    'courses' => $trackCourses,
                ];
            });

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
            'published_courses' => $courses->where('is_published', true)->count(),
        ];

        return Inertia::render('Instructor/Dashboard', [
            'stats' => $stats,
            'batches' => $batches,
            'tracks' => $tracks,
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'title_ar' => $course->title_ar,
                    'translated_title' => $course->translated_title,
                    'description' => $course->description,
                    'description_ar' => $course->description_ar,
                    'translated_description' => $course->translated_description,
                    'slug' => $course->slug,
                    'is_published' => $course->is_published ?? false,
                    'enrollments_count' => $course->enrollments_count ?? 0,
                    'track' => $course->track ? [
                        'id' => $course->track->id,
                        'name' => $course->track->name,
                        'name_ar' => $course->track->name_ar,
                        'translated_name' => $course->track->translated_name,
                    ] : null,
                ];
            }),
            'user' => [
                'id' => $instructor->id,
                'name' => $instructor->name,
                'email' => $instructor->email,
                'national_id' => $instructor->national_id,
                'role' => $instructor->role,
                'bio' => $instructor->bio,
                'avatar' => $instructor->avatar,
                'created_at' => $instructor->created_at,
            ],
        ]);
    }
}

