<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\User;
use App\Models\Enrollment;
use Modules\Courses\Models\Course;
use Illuminate\Support\Collection;

class BatchService
{
    /**
     * Get batches for a course with optimized queries
     */
    public function getBatchesForCourse(Course $course): Collection
    {
        return Batch::where('course_id', $course->id)
            ->with(['instructor:id,name,email', 'enrollments:id,batch_id'])
            ->withCount('enrollments')
            ->latest()
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'name' => $batch->name,
                    'name_ar' => $batch->name_ar,
                    'translated_name' => $batch->translated_name,
                    'description' => $batch->description,
                    'description_ar' => $batch->description_ar,
                    'translated_description' => $batch->translated_description,
                    'start_date' => $batch->start_date,
                    'end_date' => $batch->end_date,
                    'max_students' => $batch->max_students,
                    'is_active' => $batch->is_active,
                    'instructor' => $batch->instructor ? [
                        'id' => $batch->instructor->id,
                        'name' => $batch->instructor->name,
                        'email' => $batch->instructor->email,
                    ] : null,
                    'enrollments_count' => $batch->enrollments_count,
                    'created_at' => $batch->created_at,
                ];
            });
    }

    /**
     * Get active instructors for batch assignment
     */
    public function getActiveInstructors(): Collection
    {
        return User::where('role', 'instructor')
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new batch
     */
    public function createBatch(Course $course, array $data): Batch
    {
        // Verify instructor role
        $instructor = User::findOrFail($data['instructor_id']);
        if ($instructor->role !== 'instructor') {
            throw new \InvalidArgumentException('The selected user is not an instructor.');
        }

        return Batch::create([
            'course_id' => $course->id,
            'instructor_id' => $data['instructor_id'],
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'description' => $data['description'] ?? null,
            'description_ar' => $data['description_ar'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_students' => $data['max_students'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update batch
     */
    public function updateBatch(Batch $batch, array $data): bool
    {
        // Verify instructor role if changed
        if (isset($data['instructor_id']) && $data['instructor_id'] !== $batch->instructor_id) {
            $instructor = User::findOrFail($data['instructor_id']);
            if ($instructor->role !== 'instructor') {
                throw new \InvalidArgumentException('The selected user is not an instructor.');
            }
        }

        return $batch->update($data);
    }

    /**
     * Get batch with students
     */
    public function getBatchWithStudents(Batch $batch): array
    {
        $batch->load(['instructor:id,name,email', 'course:id,title,slug']);

        $students = $batch->enrollments()
            ->with('student:id,name,email,national_id')
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'national_id' => $enrollment->student->national_id,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'progress' => $enrollment->progress ?? 0,
                    'status' => $enrollment->status ?? 'enrolled',
                ];
            });

        return [
            'batch' => [
                'id' => $batch->id,
                'name' => $batch->translated_name,
                'description' => $batch->translated_description,
                'course' => [
                    'id' => $batch->course->id,
                    'title' => $batch->course->translated_title,
                    'slug' => $batch->course->slug,
                ],
                'instructor' => $batch->instructor ? [
                    'id' => $batch->instructor->id,
                    'name' => $batch->instructor->name,
                    'email' => $batch->instructor->email,
                ] : null,
                'start_date' => $batch->start_date,
                'end_date' => $batch->end_date,
                'max_students' => $batch->max_students,
                'is_active' => $batch->is_active,
            ],
            'students' => $students,
        ];
    }

    /**
     * Get available students for batch (not already enrolled)
     */
    public function getAvailableStudents(Batch $batch): Collection
    {
        return User::where('role', 'student')
            ->where('is_active', true)
            ->whereDoesntHave('enrollments', function ($query) use ($batch) {
                $query->where('batch_id', $batch->id);
            })
            ->select('id', 'name', 'email', 'national_id')
            ->orderBy('name')
            ->get();
    }
}

