<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Modules\Courses\Services\LessonAttendanceService;

class ProfileController extends Controller
{
    public function __construct(
        private LessonAttendanceService $attendanceService
    ) {}

    public function show()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Profile/Show', [
            'user' => $this->formatUserData($user),
            'assignedBatches' => $user->isInstructor() ? $this->getAssignedBatches($user) : [],
            'myBatches' => $user->isStudent() ? $this->getEnrolledBatches($user) : [],
            'assignedCourses' => [],
            'myCourses' => [],
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:users,national_id,' . $user->id],
            'bio' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'national_id' => $validated['national_id'] ?? $user->national_id,
            'bio' => $validated['bio'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', __('messages.profile.updated_successfully'));
    }

    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'national_id' => $user->national_id,
            'role' => $user->role,
            'is_admin' => $user->is_admin ?? false,
            'avatar' => $user->avatar,
            'bio' => $user->bio,
            'created_at' => $user->created_at,
        ];
    }

    private function getAssignedBatches(User $user): array
    {
        $batches = Batch::where('instructor_id', $user->id)
            ->with(['course', 'enrollments.student'])
            ->withCount('enrollments')
            ->latest()
            ->get();

        // Group batches by course
        $groupedByCourse = $batches->groupBy('course_id');
        
        return $groupedByCourse->map(function ($courseBatches, $courseId) {
            $firstBatch = $courseBatches->first();
            $course = $firstBatch->course;
            
            return [
                'course' => $this->formatCourseData($course),
                'batches' => $courseBatches->map(fn ($batch) => $this->formatBatchData($batch, true))->values()->toArray(),
                'students' => $this->getAllStudentsFromBatches($courseBatches),
                'total_enrollments' => $courseBatches->sum('enrollments_count'),
            ];
        })->values()->toArray();
    }

    private function getEnrolledBatches(User $user): array
    {
        $batches = $user->enrolledBatches()
            ->with(['course', 'instructor'])
            ->latest('enrollments.enrolled_at')
            ->get();

        // Group batches by course
        $groupedByCourse = $batches->groupBy('course_id');
        
        return $groupedByCourse->map(function ($courseBatches, $courseId) use ($user) {
            $firstBatch = $courseBatches->first();
            $course = $firstBatch->course;
            
            return [
                'course' => $this->formatCourseData($course),
                'batches' => $courseBatches->map(fn ($batch) => $this->formatBatchData($batch, false, $user->id))->values()->toArray(),
            ];
        })->values()->toArray();
    }

    private function getAllStudentsFromBatches($batches): array
    {
        $allStudents = collect();
        
        foreach ($batches as $batch) {
            if ($batch->enrollments) {
                foreach ($batch->enrollments as $enrollment) {
                    if ($enrollment->student) {
                        $allStudents->push([
                            'id' => $enrollment->student->id,
                            'name' => $enrollment->student->name,
                            'email' => $enrollment->student->email,
                            'enrolled_at' => $enrollment->enrolled_at,
                            'progress' => $enrollment->progress ?? 0,
                            'status' => $enrollment->status ?? 'active',
                            'batch_id' => $batch->id,
                            'batch_name' => $batch->translated_name,
                        ]);
                    }
                }
            }
        }
        
        // Remove duplicates by student ID, keeping the most recent enrollment
        return $allStudents->unique('id')->values()->toArray();
    }

    private function formatBatchData($batch, bool $includeStudents, ?int $studentId = null): array
    {
        $data = [
            'id' => $batch->id,
            'name' => $batch->name,
            'name_ar' => $batch->name_ar,
            'translated_name' => $batch->translated_name,
            'course' => $this->formatCourseData($batch->course),
            'is_active' => $batch->is_active ?? true,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
        ];

        if ($includeStudents) {
            $data['enrollments_count'] = $batch->enrollments_count ?? 0;
            $data['students'] = $this->formatStudentsData($batch->enrollments);
        } else {
            $enrollment = $batch->pivot;
            $data['instructor'] = $batch->instructor ? [
                'id' => $batch->instructor->id,
                'name' => $batch->instructor->name,
            ] : null;
            
            // Use student_id from parameter or get from enrollment
            $currentStudentId = $studentId ?? ($enrollment->student_id ?? null);
            
            // Calculate actual progress from LessonCompletion records
            if ($currentStudentId && $batch->course) {
                $progress = $this->calculateStudentProgress($batch->course, $currentStudentId, $batch->id, $enrollment->progress ?? 0);
                $data['progress'] = $progress;
            } else {
            $data['progress'] = $enrollment->progress ?? 0;
            }
            
            $data['enrolled_at'] = $enrollment->enrolled_at;
        }

        return $data;
    }

    private function formatCourseData($course): ?array
    {
        if (!$course) {
            return null;
        }

        return [
            'id' => $course->id,
            'slug' => $course->slug,
            'title' => $course->title,
            'title_ar' => $course->title_ar,
            'translated_title' => $course->translated_title,
            'thumbnail_url' => $course->thumbnail_url,
        ];
    }

    private function formatStudentsData($enrollments): array
    {
        return $enrollments
            ->filter(fn ($enrollment) => $enrollment->student !== null)
            ->map(function ($enrollment) {
                $batch = $enrollment->batch;
                $course = $batch->course ?? null;
                
                // Calculate actual progress from LessonCompletion records
                $progress = $course 
                    ? $this->calculateStudentProgress($course, $enrollment->student_id, $batch->id, $enrollment->progress ?? 0)
                    : ($enrollment->progress ?? 0);
                
                return [
                'id' => $enrollment->student->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
                'enrolled_at' => $enrollment->enrolled_at,
                    'progress' => $progress,
                'status' => $enrollment->status ?? 'active',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Calculate student progress from LessonCompletion records
     */
    private function calculateStudentProgress($course, int $studentId, int $batchId, int $currentProgress): int
    {
        if (!$course) {
            return $currentProgress;
        }

        $student = User::find($studentId);
        if (!$student) {
            return $currentProgress;
        }

        // Sync LessonCompletion records from LessonAttendance first
        $this->attendanceService->syncCompletionsFromAttendance($student, $course, $batchId);

        $totalLessons = $course->lessons()->count();
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = \App\Models\LessonCompletion::where('student_id', $studentId)
            ->where('batch_id', $batchId)
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();

        $calculatedProgress = (int) round(($completedLessons / $totalLessons) * 100);

        // Update enrollment progress if it's different
        if ($calculatedProgress !== $currentProgress) {
            \App\Models\Enrollment::where('student_id', $studentId)
                ->where('batch_id', $batchId)
                ->update(['progress' => $calculatedProgress]);
        }

        return $calculatedProgress;
    }
}
