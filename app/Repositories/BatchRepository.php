<?php

namespace App\Repositories;

use App\Models\Batch;
use App\Models\Enrollment;
use Modules\Courses\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class BatchRepository
{
    /**
     * Get batches by course with relations
     */
    public function getByCourse(Course $course, array $relations = []): Collection
    {
        $query = $course->batches();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get batch by ID with relations
     */
    public function findById(int $id, array $relations = []): ?Batch
    {
        $query = Batch::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * Get batch by ID or fail
     */
    public function findByIdOrFail(int $id, array $relations = []): Batch
    {
        $query = Batch::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->findOrFail($id);
    }

    /**
     * Create a new batch
     */
    public function create(array $data): Batch
    {
        return Batch::create($data);
    }

    /**
     * Update batch
     */
    public function update(Batch $batch, array $data): bool
    {
        return $batch->update($data);
    }

    /**
     * Delete batch
     */
    public function delete(Batch $batch): bool
    {
        return $batch->delete();
    }

    /**
     * Find active batch for course (hasn't ended yet)
     */
    public function findActiveBatchForCourse(int $courseId): ?Batch
    {
        return Batch::where('course_id', $courseId)
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now()->toDateString());
            })
            ->first();
    }

    /**
     * Get batches with enrollments count
     */
    public function getWithEnrollmentsCount(Course $course, array $relations = []): Collection
    {
        $query = $course->batches();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get available students for batch (not enrolled)
     */
    public function getAvailableStudents(Batch $batch): Collection
    {
        $enrolledIds = $batch->enrollments->pluck('student_id');

        return \App\Models\User::where(function($query) {
                $query->where('role', 'student')
                      ->orWhereHas('roles', function($q) {
                          $q->where('slug', 'student')
                            ->orWhere('name', 'student');
                      });
            })
            ->where('is_active', true)
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->filter(function($user) {
                return $user->isStudent();
            })
            ->values();
    }

    /**
     * Check if user is enrolled in batch
     */
    public function isUserEnrolled(Batch $batch, int $userId): bool
    {
        return Enrollment::where('batch_id', $batch->id)
            ->where('student_id', $userId)
            ->exists();
    }

    /**
     * Enroll user in batch
     */
    public function enrollUser(Batch $batch, int $userId, array $data = []): Enrollment
    {
        // Check max_students limit if set
        if ($batch->max_students !== null) {
            $currentStudents = Enrollment::where('batch_id', $batch->id)->count();
            
            if ($currentStudents >= $batch->max_students) {
                $validator = validator([], []);
                $validator->errors()->add('student_id', __('admin.max_students_exceeded', [
                    'max' => $batch->max_students,
                    'current' => $currentStudents,
                ]));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        return Enrollment::create(array_merge([
            'batch_id' => $batch->id,
            'student_id' => $userId,
            'enrolled_at' => now(),
            'status' => 'enrolled',
            'progress' => 0,
        ], $data));
    }

    /**
     * Remove user from batch
     */
    public function removeUser(Batch $batch, int $userId): bool
    {
        return Enrollment::where('batch_id', $batch->id)
            ->where('student_id', $userId)
            ->delete() > 0;
    }
}

