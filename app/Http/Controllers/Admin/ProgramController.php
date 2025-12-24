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
            $trackProgress = $trackProgresses->where('track_id', $track->id);
            $completedProgress = $trackProgress->where('status', 'completed');
            
            $trackCompletionStats[] = [
                'track_id' => $track->id,
                'track_name' => $track->translated_name ?? $track->name,
                'total_students' => $trackProgress->unique('student_id')->count(),
                'completed_students' => $completedProgress->unique('student_id')->count(),
                'completion_percentage' => $trackProgress->unique('student_id')->count() > 0 
                    ? round(($completedProgress->unique('student_id')->count() / $trackProgress->unique('student_id')->count()) * 100, 1)
                    : 0,
            ];
        }
        
        // Student progress: how many tracks each student completed in this program
        $studentProgress = [];
        $uniqueStudentIds = $trackProgresses->pluck('student_id')->unique();
        
        foreach ($uniqueStudentIds as $studentId) {
            $studentTrackProgresses = $trackProgresses->where('student_id', $studentId);
            $completedTracks = $studentTrackProgresses->where('status', 'completed')->count();
            $totalTracks = $studentTrackProgresses->count();
            
            if ($totalTracks > 0) {
                $student = User::find($studentId);
                if ($student) {
                    $studentProgress[] = [
                        'student_id' => $studentId,
                        'student_name' => $student->name,
                        'completed_tracks' => $completedTracks,
                        'total_tracks' => $totalTracks,
                        'progress_percentage' => round(($completedTracks / $totalTracks) * 100, 1),
                    ];
                }
            }
        }
        
        // Overall program completion
        $totalStudents = $uniqueStudentIds->count();
        $studentsCompletedAllTracks = collect($studentProgress)
            ->where('completed_tracks', '>=', $tracks->count())
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

