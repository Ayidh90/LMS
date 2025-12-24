<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Track;
use App\Models\TrackProgress;
use App\Services\TrackProgressService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            
            if (!$user || (!$user->isStudent() && !$user->hasPermission('dashboard.student'))) {
                abort(403, __('messages.forbidden'));
            }
            
            return $next($request);
        });
    }

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
            ->with(['batch.course.track', 'batch.instructor']) // Load through batches
            ->latest()
            ->get();

        // Get tracks that have courses the student is enrolled in
        $courseIds = $enrollments->pluck('batch.course.id')->filter()->unique()->toArray();
        $tracks = Track::whereHas('courses', function($query) use ($courseIds) {
            $query->whereIn('id', $courseIds);
        })->with(['courses' => function($query) use ($courseIds) {
            $query->whereIn('id', $courseIds);
        }])->get();

        // Get track progress for each track
        $trackProgressService = app(TrackProgressService::class);
        $tracksWithProgress = $tracks->map(function($track) use ($student, $trackProgressService, $enrollments) {
            $trackProgress = TrackProgress::where('student_id', $student->id)
                ->where('track_id', $track->id)
                ->first();

            // If no track progress exists, try to initialize it
            if (!$trackProgress) {
                // Check if student has enrollments in any course in this track
                $trackCourseIds = $track->courses->pluck('id')->toArray();
                $hasEnrollment = $enrollments->filter(function($enrollment) use ($trackCourseIds) {
                    $courseId = $enrollment->batch->course->id ?? null;
                    return $courseId && in_array($courseId, $trackCourseIds);
                })->isNotEmpty();

                if ($hasEnrollment) {
                    $trackProgress = $trackProgressService->initializeTrackProgress($student, $track);
                }
            }

            // Calculate progress if track progress exists
            $progress = 0;
            if ($trackProgress) {
                $trackProgress->calculateProgress();
                $trackProgress->refresh();
                $progress = $trackProgress->progress ?? 0;
            }

            // Get courses in this track with enrollment info
            $coursesInTrack = $track->courses->map(function($course) use ($enrollments, $student) {
                $enrollment = $enrollments->first(function($enrollment) use ($course) {
                    return ($enrollment->batch->course->id ?? null) === $course->id;
                });

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'title_ar' => $course->title_ar,
                    'translated_title' => $course->translated_title,
                    'slug' => $course->slug,
                    'progress' => $enrollment ? ($enrollment->progress ?? 0) : 0,
                    'status' => $enrollment ? ($enrollment->status ?? 'enrolled') : null,
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
                'progress' => $progress,
                'courses' => $coursesInTrack,
                'courses_count' => $coursesInTrack->count(),
            ];
        })->filter(function($track) {
            // Only include tracks where student has at least one enrollment
            return $track['courses_count'] > 0;
        })->values();

        return Inertia::render('Student/Dashboard', [
            'stats' => $stats,
            'enrollments' => $enrollments,
            'tracks' => $tracksWithProgress,
            'user' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'national_id' => $student->national_id,
                'role' => $student->role,
                'bio' => $student->bio,
                'avatar' => $student->avatar,
                'created_at' => $student->created_at,
            ],
        ]);
    }
}

