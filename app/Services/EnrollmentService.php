<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Batch;
use App\Models\User;
use Modules\Courses\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    /**
     * Add students to batch
     */
    public function addStudentsToBatch(Batch $batch, Course $course, array $studentIds): int
    {
        $added = 0;

        // Get students in one query
        $students = User::whereIn('id', $studentIds)
            ->where('role', 'student')
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        // Get existing enrollments in one query
        $existingEnrollments = Enrollment::where('batch_id', $batch->id)
            ->whereIn('student_id', $students)
            ->pluck('student_id')
            ->toArray();

        // Prepare bulk insert data
        $enrollmentsToCreate = [];
        $now = now();

        foreach ($students as $studentId) {
            if (!in_array($studentId, $existingEnrollments)) {
                $enrollmentsToCreate[] = [
                    'student_id' => $studentId,
                    'batch_id' => $batch->id,
                    'course_id' => $course->id,
                    'status' => 'enrolled',
                    'progress' => 0,
                    'enrolled_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $added++;
            }
        }

        // Bulk insert for better performance
        if (!empty($enrollmentsToCreate)) {
            DB::table('enrollments')->insert($enrollmentsToCreate);
        }

        return $added;
    }

    /**
     * Remove student from batch
     */
    public function removeStudentFromBatch(Batch $batch, User $student): bool
    {
        $enrollment = Enrollment::where('batch_id', $batch->id)
            ->where('student_id', $student->id)
            ->first();

        if ($enrollment) {
            return $enrollment->delete();
        }

        return false;
    }

    /**
     * Get enrollments for student with courses
     */
    public function getStudentEnrollments(User $student): Collection
    {
        return Enrollment::where('student_id', $student->id)
            ->with(['batch.course:id,title,slug,thumbnail_url', 'batch.instructor:id,name'])
            ->latest('enrolled_at')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->batch->course->id,
                    'title' => $enrollment->batch->course->translated_title,
                    'slug' => $enrollment->batch->course->slug,
                    'thumbnail_url' => $enrollment->batch->course->thumbnail_url,
                    'batch' => [
                        'id' => $enrollment->batch->id,
                        'name' => $enrollment->batch->translated_name,
                        'instructor' => $enrollment->batch->instructor ? [
                            'id' => $enrollment->batch->instructor->id,
                            'name' => $enrollment->batch->instructor->name,
                        ] : null,
                    ],
                    'progress' => $enrollment->progress ?? 0,
                    'status' => $enrollment->status ?? 'enrolled',
                    'enrolled_at' => $enrollment->enrolled_at,
                ];
            });
    }

    /**
     * Get enrolled course IDs for student
     */
    public function getEnrolledCourseIds(User $student): array
    {
        return Enrollment::where('student_id', $student->id)
            ->join('batches', 'enrollments.batch_id', '=', 'batches.id')
            ->pluck('batches.course_id')
            ->unique()
            ->toArray();
    }

    /**
     * Get enrolled batch IDs for student
     */
    public function getEnrolledBatchIds(User $student): array
    {
        return Enrollment::where('student_id', $student->id)
            ->pluck('batch_id')
            ->toArray();
    }
}

