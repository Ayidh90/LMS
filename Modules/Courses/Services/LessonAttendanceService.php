<?php

namespace Modules\Courses\Services;

use App\Models\Enrollment;
use App\Models\LessonAttendance;
use App\Models\LessonCompletion;
use App\Models\UserQuestionAnswer;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonAttendanceService
{
    /**
     * Auto-mark attendance when student opens a lesson
     * Rules:
     * 1. Text lesson (no video, no questions) -> Auto-mark as present
     * 2. Video lesson (uploaded file) -> Mark after 80% watched
     * 3. Video lesson (link) -> Auto-mark immediately
     * 4. Lesson with questions -> Mark after all answered
     * 5. Instructor marked -> Don't override
     */
    public function autoMarkAttendance(User $student, Course $course, Lesson $lesson): void
    {
        // Check if user's effective role is student (supports multiple roles)
        $effectiveRole = $student->getEffectiveRole();
        if (!$student->isStudent() || $effectiveRole !== 'student') {
            return;
        }

        $enrollments = $this->getStudentEnrollments($student, $course);
        if ($enrollments->isEmpty()) {
            return;
        }

        $lesson->load('questions');
        
        $hasVideo = !empty($lesson->video_url);
        $hasQuestions = $lesson->questions && $lesson->questions->count() > 0;
        $isUploadedVideo = $lesson->type === 'video_file';
        $isVideoLink = $hasVideo && !$isUploadedVideo;
        $isLiveLesson = $lesson->type === 'live';

        foreach ($enrollments as $enrollment) {
            // Check if instructor has already marked attendance (instructor takes priority)
            $existingAttendance = LessonAttendance::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->where('batch_id', $enrollment->batch_id)
                ->first();
            
            if ($existingAttendance && $existingAttendance->marked_by !== $student->id) {
                // Instructor has already marked, don't override
                continue;
            }

            // Don't auto-mark live lessons - they require clicking the link
            if ($isLiveLesson) {
                continue;
            }

            // Auto-mark for text lessons or video links without questions
            if ((!$hasVideo && !$hasQuestions) || ($isVideoLink && !$hasQuestions)) {
                $this->createAttendanceRecord($lesson, $student, $enrollment->batch_id, 'present', 'Auto-marked: Lesson opened');
                // Ensure enrollment relationships are loaded
                $enrollment->load(['student', 'batch.course']);
                $this->markLessonCompleted($lesson, $student, $enrollment, true);
            }
        }
    }
    
    /**
     * Mark attendance when student clicks on live meeting link
     */
    public function markAttendanceForLiveLesson(User $student, Course $course, Lesson $lesson): void
    {
        // Check if user's effective role is student (supports multiple roles)
        $effectiveRole = $student->getEffectiveRole();
        if (!$student->isStudent() || $effectiveRole !== 'student' || $lesson->type !== 'live') {
            return;
        }

        $enrollments = $this->getStudentEnrollments($student, $course);
        if ($enrollments->isEmpty()) {
            return;
        }

        foreach ($enrollments as $enrollment) {
            // Check if instructor has already marked attendance (instructor takes priority)
            $existingAttendance = LessonAttendance::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->where('batch_id', $enrollment->batch_id)
                ->first();
            
            if ($existingAttendance && $existingAttendance->marked_by !== $student->id) {
                // Instructor has already marked, don't override
                continue;
            }

            // Mark attendance as present when student clicks the live meeting link
            $this->createAttendanceRecord($lesson, $student, $enrollment->batch_id, 'present', 'Auto-marked: Live meeting link clicked');
            // Ensure enrollment relationships are loaded
            $enrollment->load(['student', 'batch.course']);
            $this->markLessonCompleted($lesson, $student, $enrollment, true);
        }
    }

    /**
     * Mark attendance after video watch (80% requirement)
     */
    public function markAttendanceAfterVideoWatch(User $student, Course $course, Lesson $lesson, float $watchPercentage): void
    {
        // Check if user's effective role is student (supports multiple roles)
        $effectiveRole = $student->getEffectiveRole();
        if (!$student->isStudent() || $effectiveRole !== 'student' || $watchPercentage < 80) {
            return;
        }

        $enrollments = $this->getStudentEnrollments($student, $course);
        
        foreach ($enrollments as $enrollment) {
            // Check if instructor has already marked attendance (instructor takes priority)
            $existingAttendance = LessonAttendance::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->where('batch_id', $enrollment->batch_id)
                ->first();
            
            if ($existingAttendance && $existingAttendance->marked_by !== $student->id) {
                // Instructor has already marked, don't override
                continue;
            }

            $this->createAttendanceRecord($lesson, $student, $enrollment->batch_id, 'present', "Auto-marked: Watched {$watchPercentage}% of video");
            // Ensure enrollment relationships are loaded
            $enrollment->load(['student', 'batch.course']);
            $this->markLessonCompleted($lesson, $student, $enrollment, true);
        }
    }

    /**
     * Mark attendance after all questions answered
     */
    public function markAttendanceAfterQuestions(User $student, Course $course, Lesson $lesson): void
    {
        // Check if user's effective role is student (supports multiple roles)
        $effectiveRole = $student->getEffectiveRole();
        if (!$student->isStudent() || $effectiveRole !== 'student') {
            return;
        }

        $lesson->load('questions');
        
        if (!$lesson->questions || $lesson->questions->count() === 0) {
            return;
        }

        $totalQuestions = $lesson->questions->count();
        // Count distinct questions answered by the student
        // Note: Since there's a unique constraint on (user_id, question_id), 
        // we could skip distinct, but keeping it for safety
        $answeredQuestions = UserQuestionAnswer::where('user_id', $student->id)
            ->whereIn('question_id', $lesson->questions->pluck('id'))
            ->select('question_id')
            ->distinct()
            ->count();

        if ($answeredQuestions < $totalQuestions) {
            return;
        }

        $enrollments = $this->getStudentEnrollments($student, $course);
        
        foreach ($enrollments as $enrollment) {
            // Check if instructor has already marked attendance (instructor takes priority)
            $existingAttendance = LessonAttendance::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->where('batch_id', $enrollment->batch_id)
                ->first();
            
            if ($existingAttendance && $existingAttendance->marked_by !== $student->id) {
                // Instructor has already marked, don't override
                continue;
            }

            $this->createAttendanceRecord($lesson, $student, $enrollment->batch_id, 'present', 'Auto-marked: All questions answered');
            // Ensure enrollment relationships are loaded
            $enrollment->load(['student', 'batch.course']);
            $this->markLessonCompleted($lesson, $student, $enrollment, true);
        }
    }

    /**
     * Instructor marks attendance manually (takes priority)
     */
    public function instructorMarkAttendance(Lesson $lesson, User $instructor, array $attendanceData): void
    {
        DB::transaction(function () use ($lesson, $instructor, $attendanceData) {
            $course = $lesson->course;
            $enrollmentsToUpdate = [];

            foreach ($attendanceData as $data) {
                $enrollment = Enrollment::with(['student', 'batch.course'])
                    ->where('batch_id', $data['batch_id'])
                    ->where('student_id', $data['student_id'])
                    ->first();

                if (!$enrollment) {
                    continue;
                }

                LessonAttendance::updateOrCreate(
                    [
                        'lesson_id' => $lesson->id,
                        'student_id' => $data['student_id'],
                        'batch_id' => $data['batch_id'],
                    ],
                    [
                        'marked_by' => $instructor->id,
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? null,
                        'attended_at' => $data['attended_at'] ?? now(),
                    ]
                );

                // Handle completion records based on status
                if (in_array($data['status'], ['present', 'late', 'excused'])) {
                    // Create completion record if it doesn't exist
                    $this->markLessonCompleted($lesson, $enrollment->student, $enrollment, false);
                } else {
                    // Remove completion record if status is absent
                    LessonCompletion::where('lesson_id', $lesson->id)
                        ->where('student_id', $data['student_id'])
                        ->where('batch_id', $data['batch_id'])
                        ->delete();
                }

                // Track enrollments that need progress update
                $key = $enrollment->student_id . '-' . $enrollment->batch_id;
                if (!isset($enrollmentsToUpdate[$key])) {
                    $enrollmentsToUpdate[$key] = $enrollment;
                }
            }

            // Update progress for all affected enrollments after all attendance records are created
            // This ensures progress is calculated with all attendance records
            foreach ($enrollmentsToUpdate as $enrollment) {
                // Refresh enrollment to get latest data and reload relationships
                $enrollment->refresh();
                $enrollment->load(['student', 'batch.course']);
                $this->updateEnrollmentProgress($enrollment, true);
            }
        });
    }

    /**
     * Get attendance records for a lesson
     */
    /**
     * Get students with attendance for a lesson
     */
    public function getStudentsWithAttendance($batches, Lesson $lesson, $attendances): \Illuminate\Support\Collection
    {
        $students = collect();
        $attendanceMap = $attendances->keyBy(fn($a) => $a['student_id'] . '-' . $a['batch_id']);

        foreach ($batches as $batch) {
            foreach ($batch->enrollments as $enrollment) {
                $key = $enrollment->student_id . '-' . $batch->id;
                $attendance = $attendanceMap->get($key);

                $completion = LessonCompletion::where('lesson_id', $lesson->id)
                    ->where('student_id', $enrollment->student_id)
                    ->where('batch_id', $batch->id)
                    ->first();

                $students->push([
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->translated_name ?? $batch->name,
                    'enrollment_progress' => $enrollment->progress ?? 0,
                    'attendance' => $attendance ? [
                        'status' => $attendance['status'],
                        'notes' => $attendance['notes'],
                        'attended_at' => $attendance['attended_at'],
                        'marked_by' => $attendance['student_id'] === ($attendance['marked_by'] ?? null) ? 'student' : 'instructor',
                    ] : null,
                    'completed' => $completion ? true : false,
                    'completed_at' => $completion?->completed_at,
                ]);
            }
        }

        return $students;
    }

    /**
     * Calculate attendance statistics
     */
    public function calculateAttendanceStats($attendances, $students): array
    {
        $total = $students->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $excused = $attendances->where('status', 'excused')->count();
        $notMarked = $total - $attendances->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'not_marked' => $notMarked,
            'attendance_rate' => $total > 0 ? round((($present + $late + $excused) / $total) * 100) : 0,
        ];
    }

    /**
     * Filter valid attendances for instructor
     */
    public function filterValidAttendances(array $attendances, \App\Models\User $instructor): array
    {
        return array_filter($attendances, function ($data) use ($instructor) {
            $batch = \App\Models\Batch::find($data['batch_id']);
            if (!$batch || ($batch->instructor_id !== $instructor->id && !$instructor->isAdmin())) {
                return false;
            }
            return \App\Models\Enrollment::where('batch_id', $data['batch_id'])
                ->where('student_id', $data['student_id'])
                ->exists();
        });
    }

    public function getLessonAttendances(Lesson $lesson, $batchIds): \Illuminate\Support\Collection
    {
        return LessonAttendance::where('lesson_id', $lesson->id)
            ->whereIn('batch_id', $batchIds)
            ->with(['student:id,name,email', 'batch:id,name,name_ar', 'markedBy:id,name'])
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'student_id' => $a->student_id,
                'batch_id' => $a->batch_id,
                'status' => $a->status,
                'notes' => $a->notes,
                'attended_at' => $a->attended_at,
                'marked_at' => $a->created_at,
                'marked_by' => $a->marked_by,
                'marked_by_type' => $a->marked_by === $a->student_id ? 'student' : 'instructor',
                'student' => [
                    'id' => $a->student->id,
                    'name' => $a->student->name,
                    'email' => $a->student->email,
                ],
                'batch' => [
                    'id' => $a->batch->id,
                    'name' => $a->batch->translated_name ?? $a->batch->name,
                ],
                'marker' => $a->markedBy ? [
                    'id' => $a->markedBy->id,
                    'name' => $a->markedBy->name,
                ] : null,
            ]);
    }

    /**
     * Get student's attendance for a specific lesson
     */
    public function getStudentLessonAttendance(User $student, Lesson $lesson, int $batchId): ?array
    {
        $attendance = LessonAttendance::where('lesson_id', $lesson->id)
            ->where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->with('markedBy:id,name')
            ->first();
        
        if (!$attendance) {
            return null;
        }

        return [
            'status' => $attendance->status,
            'notes' => $attendance->notes,
            'attended_at' => $attendance->attended_at,
            'marked_by' => $attendance->marked_by === $student->id ? 'student' : 'instructor',
            'marker_name' => $attendance->markedBy?->name,
        ];
    }

    /**
     * Get course progress for a student
     */
    public function getStudentCourseProgress(User $student, Course $course, int $batchId): array
    {
        $totalLessons = $course->lessons()->count();
        
        if ($totalLessons === 0) {
            return [
                'total_lessons' => 0,
                'completed_lessons' => 0,
                'progress_percentage' => 0,
                'attended_lessons' => 0,
                'attendance_rate' => 0,
            ];
        }

        // Get enrollment to update progress
        $enrollment = Enrollment::with(['student', 'batch.course'])
            ->where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->first();

        if ($enrollment) {
            // Sync completions first (without updating progress to avoid double update)
            $this->syncCompletionsFromAttendance($student, $course, $batchId, false);
            // Force update progress to ensure it's current
            $this->updateEnrollmentProgress($enrollment, false);
            // Refresh to get latest progress value
            $enrollment->refresh();
        } else {
            // Fallback: just sync completions if no enrollment found
            $this->syncCompletionsFromAttendance($student, $course, $batchId, false);
        }

        // Count completed lessons directly from database
        $completedLessons = LessonCompletion::where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();

        $attendedLessons = LessonAttendance::where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->whereIn('status', ['present', 'late', 'excused'])
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();

        $progressPercentage = $totalLessons > 0 
            ? (int) round(($completedLessons / $totalLessons) * 100) 
            : 0;

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $progressPercentage,
            'attended_lessons' => $attendedLessons,
            'attendance_rate' => $totalLessons > 0 
                ? (int) round(($attendedLessons / $totalLessons) * 100) 
                : 0,
        ];
    }

    /**
     * Sync LessonCompletion records from LessonAttendance
     * Ensures that every attended lesson (present, late, excused) has a completion record
     * This is public so it can be called from controllers
     * 
     * @param bool $updateProgress Whether to update enrollment progress after syncing (default: true)
     */
    public function syncCompletionsFromAttendance(User $student, Course $course, int $batchId, bool $updateProgress = true): void
    {
        $attendances = LessonAttendance::where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->whereIn('status', ['present', 'late', 'excused'])
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->with('lesson')
            ->get();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->first();

        if (!$enrollment) {
            return;
        }

        foreach ($attendances as $attendance) {
            // Create completion record if it doesn't exist
            LessonCompletion::firstOrCreate(
                [
                    'lesson_id' => $attendance->lesson_id,
                    'student_id' => $student->id,
                    'batch_id' => $batchId,
                ],
                ['completed_at' => $attendance->attended_at ?? now()]
            );
        }

        // Update enrollment progress after syncing (only if requested to avoid infinite loop)
        if ($updateProgress && $enrollment) {
            $this->updateEnrollmentProgress($enrollment, false);
        }
    }

    private function getStudentEnrollments(User $student, Course $course)
    {
        return Enrollment::with(['student', 'batch.course'])
            ->where('student_id', $student->id)
            ->whereHas('batch', fn($q) => $q->where('course_id', $course->id))
            ->get();
    }

    private function hasExistingAttendance(Lesson $lesson, User $student, int $batchId): bool
    {
        return LessonAttendance::where('lesson_id', $lesson->id)
            ->where('student_id', $student->id)
            ->where('batch_id', $batchId)
            ->exists();
    }

    private function createAttendanceRecord(Lesson $lesson, User $student, int $batchId, string $status, ?string $notes = null): void
    {
        LessonAttendance::firstOrCreate(
            [
                'lesson_id' => $lesson->id,
                'student_id' => $student->id,
                'batch_id' => $batchId,
            ],
            [
                'marked_by' => $student->id,
                'status' => $status,
                'notes' => $notes,
                'attended_at' => now(),
            ]
        );
    }

    /**
     * Mark lesson as completed for a student
     * 
     * @param Lesson $lesson
     * @param User $student
     * @param Enrollment $enrollment
     * @param bool $updateProgress Whether to update enrollment progress (default: true)
     */
    private function markLessonCompleted(Lesson $lesson, User $student, Enrollment $enrollment, bool $updateProgress = true): void
    {
        // Ensure enrollment relationships are loaded before proceeding
        if (!$enrollment->relationLoaded('batch') || !$enrollment->batch || !$enrollment->batch->relationLoaded('course')) {
            $enrollment->load(['student', 'batch.course']);
        }
        
        LessonCompletion::firstOrCreate(
            [
                'lesson_id' => $lesson->id,
                'student_id' => $student->id,
                'batch_id' => $enrollment->batch_id,
            ],
            ['completed_at' => now()]
        );

        // Update progress if requested (can be skipped if updating in batch)
        if ($updateProgress) {
            $this->updateEnrollmentProgress($enrollment, true);
        }
    }

    /**
     * Update enrollment progress based on LessonCompletion records
     * 
     * @param Enrollment $enrollment
     * @param bool $syncFirst Whether to sync completions from attendance first (default: true)
     */
    private function updateEnrollmentProgress(Enrollment $enrollment, bool $syncFirst = true): void
    {
        // Refresh enrollment to get latest data and reload relationships
        $enrollment->refresh();
        $enrollment->load(['student', 'batch.course']);
        
        // Ensure batch and course relationships are loaded
        if (!$enrollment->batch || !$enrollment->batch->course) {
            return;
        }
        
        $course = $enrollment->batch->course;
        $totalLessons = $course->lessons()->count();

        if ($totalLessons === 0) {
            return;
        }

        // Sync LessonCompletion records from LessonAttendance first (if requested)
        // This ensures all attendance records (from instructor or student) are reflected in progress
        if ($syncFirst) {
            $this->syncCompletionsFromAttendance($enrollment->student, $course, $enrollment->batch_id, false);
        }

        $completedLessons = LessonCompletion::where('student_id', $enrollment->student_id)
            ->where('batch_id', $enrollment->batch_id)
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();

        $progress = (int) round(($completedLessons / $totalLessons) * 100);

        // Force update enrollment progress
        $enrollment->progress = $progress;
        $enrollment->save();

        if ($completedLessons >= $totalLessons && $enrollment->status !== 'completed') {
            $enrollment->status = 'completed';
            $enrollment->completed_at = now();
            $enrollment->save();
        }

        // Update track progress if course belongs to a track
        if ($course->track_id) {
            try {
                $trackProgressService = app(\App\Services\TrackProgressService::class);
                $trackProgressService->updateCourseProgressInTrack($enrollment);
            } catch (\Exception $e) {
                // Log error but don't fail the enrollment update
                Log::warning('Failed to update track progress: ' . $e->getMessage());
            }
        }
    }
}

