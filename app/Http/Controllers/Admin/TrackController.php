<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\Program;
use App\Models\User;
use App\Http\Requests\StoreTrackRequest;
use App\Http\Requests\UpdateTrackRequest;
use App\Services\TrackService;
use App\Services\ProgramService;
use Modules\Courses\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private TrackService $trackService,
        private ProgramService $programService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'program_id' => $request->input('program_id'),
            'is_active' => $request->input('is_active'),
            'sort' => $request->input('sort', 'order'),
        ];

        $tracks = $this->trackService->getPaginatedTracks($filters, self::PER_PAGE);
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();

        return Inertia::render('Admin/Tracks/Index', [
            'tracks' => $tracks,
            'programs' => $programs,
            'filters' => $request->only(['search', 'program_id', 'is_active', 'sort']),
        ]);
    }

    public function create(Request $request)
    {
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();
        $selectedProgramId = $request->input('program_id') ? (int) $request->input('program_id') : null;

        return Inertia::render('Admin/Tracks/Create', [
            'programs' => $programs,
            'selectedProgramId' => $selectedProgramId,
        ]);
    }

    public function store(StoreTrackRequest $request)
    {
        $track = $this->trackService->createTrack(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track created successfully.'));
    }

    public function show(Track $track)
    {
        $track = $this->trackService->getTrackById($track->id);
        
        // Get available courses (only courses without track_id - cannot add courses from other tracks)
        $availableCourses = Course::whereNull('track_id')
            ->select('id', 'title', 'title_ar', 'slug', 'track_id')
            ->orderBy('title')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->translated_title ?? $course->title,
                    'slug' => $course->slug,
                    'has_track' => false,
                ];
            });
        
        // Get progress statistics
        $progressStats = $this->getTrackProgressStatistics($track);
        
        return Inertia::render('Admin/Tracks/Show', [
            'track' => $track,
            'availableCourses' => $availableCourses,
            'progressStats' => $progressStats,
        ]);
    }
    
    /**
     * Get track progress statistics
     */
    private function getTrackProgressStatistics(Track $track): array
    {
        $courses = $track->courses;
        $courseIds = $courses->pluck('id')->toArray();
        
        // Get all enrollments for courses in this track
        $batchIds = \App\Models\Batch::whereIn('course_id', $courseIds)->pluck('id')->toArray();
        $enrollments = \App\Models\Enrollment::whereIn('batch_id', $batchIds)->get();
        
        // Course completion stats: how many students completed each course
        $courseCompletionStats = [];
        foreach ($courses as $course) {
            $courseBatchIds = \App\Models\Batch::where('course_id', $course->id)->pluck('id')->toArray();
            $courseEnrollments = $enrollments->whereIn('batch_id', $courseBatchIds);
            $completedEnrollments = $courseEnrollments->where('status', 'completed');
            
            $courseCompletionStats[] = [
                'course_id' => $course->id,
                'course_title' => $course->translated_title ?? $course->title,
                'total_students' => $courseEnrollments->unique('student_id')->count(),
                'completed_students' => $completedEnrollments->unique('student_id')->count(),
                'completion_percentage' => $courseEnrollments->unique('student_id')->count() > 0 
                    ? round(($completedEnrollments->unique('student_id')->count() / $courseEnrollments->unique('student_id')->count()) * 100, 1)
                    : 0,
            ];
        }
        
        // Student progress: how many courses each student completed in this track
        $studentProgress = [];
        $uniqueStudentIds = $enrollments->pluck('student_id')->unique();
        
        foreach ($uniqueStudentIds as $studentId) {
            $studentEnrollments = $enrollments->where('student_id', $studentId);
            $completedCourses = 0;
            $totalCourses = 0;
            
            foreach ($courses as $course) {
                $courseBatchIds = \App\Models\Batch::where('course_id', $course->id)->pluck('id')->toArray();
                $studentCourseEnrollments = $studentEnrollments->whereIn('batch_id', $courseBatchIds);
                
                if ($studentCourseEnrollments->isNotEmpty()) {
                    $totalCourses++;
                    if ($studentCourseEnrollments->where('status', 'completed')->isNotEmpty()) {
                        $completedCourses++;
                    }
                }
            }
            
            if ($totalCourses > 0) {
                $student = User::find($studentId);
                if ($student) {
                    $studentProgress[] = [
                        'student_id' => $studentId,
                        'student_name' => $student->name,
                        'completed_courses' => $completedCourses,
                        'total_courses' => $totalCourses,
                        'progress_percentage' => round(($completedCourses / $totalCourses) * 100, 1),
                    ];
                }
            }
        }
        
        // Overall track completion
        $totalStudents = $uniqueStudentIds->count();
        $studentsCompletedAllCourses = collect($studentProgress)
            ->where('completed_courses', '>=', $courses->count())
            ->count();
        
        return [
            'course_completion_stats' => $courseCompletionStats,
            'student_progress' => $studentProgress,
            'total_students' => $totalStudents,
            'students_completed_all' => $studentsCompletedAllCourses,
            'overall_completion_percentage' => $totalStudents > 0 
                ? round(($studentsCompletedAllCourses / $totalStudents) * 100, 1)
                : 0,
        ];
    }
    
    /**
     * Add courses to track
     */
    public function addCourses(Request $request, Track $track)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);
        
        $courseIds = $request->input('course_ids');
        
        // Check if any course already belongs to another track
        $coursesWithTracks = Course::whereIn('id', $courseIds)
            ->whereNotNull('track_id')
            ->where('track_id', '!=', $track->id)
            ->get();
        
        if ($coursesWithTracks->isNotEmpty()) {
            $courseTitles = $coursesWithTracks->pluck('translated_title', 'id')->toArray();
            return back()->withErrors([
                'course_ids' => __('Some courses already belong to another track and cannot be added: :courses', [
                    'courses' => implode(', ', array_values($courseTitles))
                ]),
            ]);
        }
        
        // Update courses to belong to this track
        Course::whereIn('id', $courseIds)->update(['track_id' => $track->id]);
        
        return back()->with('success', __('Courses added to track successfully.'));
    }
    
    /**
     * Remove course from track
     */
    public function removeCourse(Request $request, Track $track, Course $course)
    {
        // Verify course belongs to this track
        if ($course->track_id !== $track->id) {
            return back()->withErrors([
                'course' => __('Course does not belong to this track.'),
            ]);
        }
        
        // Remove course from track (set track_id to null)
        $course->update(['track_id' => null]);
        
        return back()->with('success', __('Course removed from track successfully.'));
    }

    public function edit(Track $track)
    {
        $track = $this->trackService->getTrackById($track->id);
        $programs = $this->programService->getPaginatedPrograms([], 100)->items();
        
        return Inertia::render('Admin/Tracks/Edit', [
            'track' => $track,
            'programs' => $programs,
        ]);
    }

    public function update(UpdateTrackRequest $request, Track $track)
    {
        $this->trackService->updateTrack(
            $track,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track updated successfully.'));
    }

    public function destroy(Track $track)
    {
        $this->trackService->deleteTrack($track);

        return redirect()
            ->route('admin.tracks.index')
            ->with('success', __('Track deleted successfully.'));
    }
}

