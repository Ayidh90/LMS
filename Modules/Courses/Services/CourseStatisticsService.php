<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\LessonCompletion;
use Modules\Courses\Models\Question;
use Modules\Courses\Services\LessonAttendanceService;
use Illuminate\Support\Facades\Log;

class CourseStatisticsService
{
    public function __construct(
        private LessonAttendanceService $attendanceService
    ) {}

    /**
     * Get course statistics
     */
    public function getCourseStatistics(Course $course): array
    {
        $batches = $course->batches;
        $batchIds = $batches->pluck('id')->toArray();
        $lessonIds = $course->lessons()->pluck('id')->toArray();

        $enrollments = $this->getEnrollmentsQuery($batchIds);
        $totalEnrollments = $enrollments->count();
        $totalStudents = $enrollments->distinct('student_id')->count();

        $totalLessons = $course->lessons()->count();
        $totalSections = $course->sections()->count();
        $totalQuestions = $this->getTotalQuestions($lessonIds);
        $completedLessons = $this->getCompletedLessons($lessonIds, $batchIds, $enrollments);
        $completionRate = $this->calculateCompletionRate($completedLessons, $totalStudents, $totalLessons);
        $lessonCompletionStats = $this->getLessonCompletionStats($course, $batchIds, $lessonIds, $totalLessons);

        return [
            'total_batches' => $batches->count(),
            'total_enrollments' => $totalEnrollments,
            'total_students' => $totalStudents,
            'total_lessons' => $totalLessons,
            'total_sections' => $totalSections,
            'total_questions' => $totalQuestions,
            'completed_lessons' => $completedLessons,
            'completion_rate' => $completionRate,
            'lesson_completion_stats' => $lessonCompletionStats,
        ];
    }

    /**
     * Get enrollments query
     */
    private function getEnrollmentsQuery(array $batchIds)
    {
        $enrollments = Enrollment::query();
        if (!empty($batchIds)) {
            $enrollments->whereIn('batch_id', $batchIds);
        } else {
            $enrollments->whereRaw('1 = 0');
        }
        return $enrollments;
    }

    /**
     * Get total questions count
     */
    private function getTotalQuestions(array $lessonIds): int
    {
        if (empty($lessonIds)) {
            return 0;
        }
        return Question::whereIn('lesson_id', $lessonIds)->count();
    }

    /**
     * Get completed lessons count
     */
    private function getCompletedLessons(array $lessonIds, array $batchIds, $enrollments): int
    {
        if (empty($lessonIds) || empty($batchIds)) {
            return 0;
        }

        $enrolledStudentIds = $enrollments->pluck('student_id')->unique()->toArray();
        return LessonCompletion::whereIn('lesson_id', $lessonIds)
            ->whereIn('batch_id', $batchIds)
            ->whereIn('student_id', $enrolledStudentIds)
            ->count();
    }

    /**
     * Get lesson completion statistics per student
     */
    private function getLessonCompletionStats(Course $course, array $batchIds, array $lessonIds, int $totalLessons): array
    {
        if (empty($batchIds) || empty($lessonIds)) {
            return [];
        }

        $enrollmentsCollection = $this->getEnrollmentsQuery($batchIds)->get();
        $uniqueStudentIds = $enrollmentsCollection->pluck('student_id')->unique();
        $stats = [];

        foreach ($uniqueStudentIds as $studentId) {
            $student = User::find($studentId);
            if (!$student) {
                continue;
            }

            $studentEnrollments = $enrollmentsCollection->where('student_id', $studentId);
            $studentBatchIds = $studentEnrollments->pluck('batch_id')->toArray();

            $this->syncStudentCompletions($student, $course, $studentBatchIds);
            $completedLessonsCount = $this->getStudentCompletedLessons($studentId, $lessonIds, $studentBatchIds);
            $progressPercentage = $totalLessons > 0 
                ? round(($completedLessonsCount / $totalLessons) * 100, 1) 
                : 0;

            $stats[] = [
                'student_id' => $studentId,
                'student_name' => $student->name,
                'completed_lessons' => $completedLessonsCount,
                'total_lessons' => $totalLessons,
                'progress_percentage' => $progressPercentage,
            ];
        }

        return $stats;
    }

    /**
     * Sync student completions from attendance
     */
    private function syncStudentCompletions(User $student, Course $course, array $batchIds): void
    {
        foreach ($batchIds as $batchId) {
            try {
                $this->attendanceService->syncCompletionsFromAttendance(
                    $student,
                    $course,
                    $batchId,
                    false
                );
            } catch (\Exception $e) {
                Log::warning("Failed to sync completions for student {$student->id} in batch {$batchId}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get student completed lessons count
     */
    private function getStudentCompletedLessons(int $studentId, array $lessonIds, array $batchIds): int
    {
        return LessonCompletion::where('student_id', $studentId)
            ->whereIn('lesson_id', $lessonIds)
            ->whereIn('batch_id', $batchIds)
            ->distinct('lesson_id')
            ->count();
    }

    /**
     * Calculate completion rate
     */
    private function calculateCompletionRate(int $completedLessons, int $totalStudents, int $totalLessons): float
    {
        if ($totalLessons === 0) {
            return 0;
        }

        $totalPossibleCompletions = $totalStudents * $totalLessons;
        if ($totalPossibleCompletions === 0) {
            return 0;
        }

        return round(($completedLessons / $totalPossibleCompletions) * 100, 2);
    }
}

