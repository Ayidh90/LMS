<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Services\ProgramService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgramController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private ProgramService $programService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'is_active' => $request->input('is_active'),
            'sort' => $request->input('sort', 'order'),
        ];

        $programs = $this->programService->getPaginatedPrograms($filters, self::PER_PAGE);

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search', 'is_active', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Programs/Create');
    }

    public function store(StoreProgramRequest $request)
    {
        $program = $this->programService->createProgram(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program created successfully.'));
    }

    public function show(Program $program)
    {
        $program = $this->programService->getProgramById($program->id);
        
        // Get progress statistics
        $progressStats = $this->getProgramProgressStatistics($program);
        
        return Inertia::render('Admin/Programs/Show', [
            'program' => $program,
            'progressStats' => $progressStats,
        ]);
    }
    
    /**
     * Get program progress statistics
     */
    private function getProgramProgressStatistics(Program $program): array
    {
        $tracks = $program->tracks;
        $trackIds = $tracks->pluck('id')->toArray();
        
        // Get all track progress records for this program
        $trackProgresses = \App\Models\TrackProgress::whereIn('track_id', $trackIds)->get();
        
        // Track completion stats: how many students completed each track
        $trackCompletionStats = [];
        foreach ($tracks as $track) {
            // Get all course IDs in this track
            $courseIds = $track->courses()->pluck('id')->toArray();
            
            // Get all batch IDs for courses in this track
            $batchIds = \App\Models\Batch::whereIn('course_id', $courseIds)->pluck('id')->toArray();
            
            // Get all unique students enrolled in batches of courses in this track
            $enrolledStudentIds = \App\Models\Enrollment::whereIn('batch_id', $batchIds)
                ->distinct()
                ->pluck('student_id')
                ->toArray();
            
            $totalStudents = count($enrolledStudentIds);
            
            // Get unique students who completed this track (from TrackProgress)
            $completedProgress = $trackProgresses->where('track_id', $track->id)->where('status', 'completed');
            $completedStudentIds = $completedProgress->pluck('student_id')->unique()->toArray();
            $completedStudents = count($completedStudentIds);
            
            $trackCompletionStats[] = [
                'track_id' => $track->id,
                'track_name' => $track->translated_name ?? $track->name,
                'total_students' => $totalStudents,
                'completed_students' => $completedStudents,
                'completion_percentage' => $totalStudents > 0 
                    ? round(($completedStudents / $totalStudents) * 100, 1)
                    : 0,
            ];
        }
        
        // Student progress: how many tracks each student completed in this program
        // Get all students enrolled in any course in any track of this program
        $allCourseIds = \Modules\Courses\Models\Course::whereIn('track_id', $trackIds)->pluck('id')->toArray();
        $allBatchIds = \App\Models\Batch::whereIn('course_id', $allCourseIds)->pluck('id')->toArray();
        $allEnrolledStudentIds = \App\Models\Enrollment::whereIn('batch_id', $allBatchIds)
            ->distinct()
            ->pluck('student_id')
            ->toArray();
        
        $studentProgress = [];
        $totalTracksInProgram = $tracks->count();
        
        foreach ($allEnrolledStudentIds as $studentId) {
            $student = User::find($studentId);
            if (!$student) {
                continue;
            }
            
            // Get all track progress records for this student in this program
            $studentTrackProgresses = $trackProgresses->where('student_id', $studentId);
            
            // Count completed tracks (status = 'completed')
            $completedTracks = $studentTrackProgresses->where('status', 'completed')->count();
            
            $studentProgress[] = [
                'student_id' => $studentId,
                'student_name' => $student->name,
                'completed_tracks' => $completedTracks,
                'total_tracks' => $totalTracksInProgram, // Total tracks in program
                'progress_percentage' => $totalTracksInProgram > 0 
                    ? round(($completedTracks / $totalTracksInProgram) * 100, 1)
                    : 0,
            ];
        }
        
        // Overall program completion
        $totalStudents = count($allEnrolledStudentIds);
        $studentsCompletedAllTracks = collect($studentProgress)
            ->where('completed_tracks', '>=', $totalTracksInProgram)
            ->count();
        
        return [
            'track_completion_stats' => $trackCompletionStats,
            'student_progress' => $studentProgress,
            'total_students' => $totalStudents,
            'students_completed_all' => $studentsCompletedAllTracks,
            'overall_completion_percentage' => $totalStudents > 0 
                ? round(($studentsCompletedAllTracks / $totalStudents) * 100, 1)
                : 0,
        ];
    }

    public function edit(Program $program)
    {
        $program = $this->programService->getProgramById($program->id);
        
        return Inertia::render('Admin/Programs/Edit', [
            'program' => $program,
        ]);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $this->programService->updateProgram(
            $program,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program updated successfully.'));
    }

    public function destroy(Program $program)
    {
        $this->programService->deleteProgram($program);

        return redirect()
            ->route('admin.programs.index')
            ->with('success', __('Program deleted successfully.'));
    }
}

