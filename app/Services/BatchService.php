<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\User;
use App\Models\Enrollment;
use App\Repositories\BatchRepository;
use Modules\Courses\Models\Course;
use Illuminate\Support\Collection;

class BatchService
{
    public function __construct(
        private BatchRepository $repository
    ) {}
    /**
     * Get batches for a course with optimized queries
     */
    public function getBatchesForCourse(Course $course): Collection
    {
        return $this->repository->getWithEnrollmentsCount($course, ['instructor:id,name,email'])
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
     * Supports users with multiple roles
     */
    public function getActiveInstructors(): Collection
    {
        // Check both legacy role field and Spatie roles (supports multiple roles)
        $instructors = User::where(function($query) {
                $query->where('role', 'instructor')
                      ->orWhereHas('roles', function($q) {
                          $q->where('slug', 'instructor')
                            ->orWhere('name', 'instructor');
                      });
            })
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->filter(function($user) {
                // Filter to ensure user has instructor role using isInstructor() method
                return $user->isInstructor();
            })
            ->values();
        
        return $instructors;
    }

    /**
     * Check if there's an active batch for course
     */
    public function hasActiveBatch(Course $course): bool
    {
        return $this->repository->findActiveBatchForCourse($course->id) !== null;
    }

    /**
     * Get active batch for course
     */
    public function getActiveBatch(Course $course): ?Batch
    {
        return $this->repository->findActiveBatchForCourse($course->id);
    }

    /**
     * Create a new batch
     */
    public function createBatch(Course $course, array $data): Batch
    {
        // Verify instructor role
        $instructor = User::findOrFail($data['instructor_id']);
        if (!$instructor->isInstructor()) {
            throw new \InvalidArgumentException('The selected user is not an instructor.');
        }

        $batchData = array_merge($data, [
            'course_id' => $course->id,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $this->repository->create($batchData);
    }

    /**
     * Update batch
     */
    public function updateBatch(Batch $batch, array $data): bool
    {
        // Verify instructor role if changed
        if (isset($data['instructor_id']) && $data['instructor_id'] !== $batch->instructor_id) {
            $instructor = User::findOrFail($data['instructor_id']);
            if (!$instructor->isInstructor()) {
                throw new \InvalidArgumentException('The selected user is not an instructor.');
            }
        }

        return $this->repository->update($batch, $data);
    }

    /**
     * Delete batch
     */
    public function deleteBatch(Batch $batch): bool
    {
        return $this->repository->delete($batch);
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
        return $this->repository->getAvailableStudents($batch);
    }

    /**
     * Get batches for instructor
     */
    public function getBatchesForInstructor(int $instructorId): Collection
    {
        return Batch::where('instructor_id', $instructorId)
            ->with(['course'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($batch) => $this->formatBatchForInstructor($batch));
    }

    /**
     * Get instructor batches for a course
     */
    public function getInstructorBatchesForCourse(Course $course, int $instructorId): Collection
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', $instructorId)
            ->with(['enrollments.student'])
            ->get();
    }

    /**
     * Format batch for instructor view
     */
    public function formatBatchForInstructor(Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'course' => [
                'id' => $batch->course->id,
                'title' => $batch->course->translated_title,
                'slug' => $batch->course->slug,
            ],
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
            'enrollments_count' => $batch->enrollments_count ?? 0,
        ];
    }

    /**
     * Authorize access to batch
     */
    public function authorizeBatchAccess(Batch $batch, User $user): void
    {
        if ($batch->instructor_id !== $user->id && !$user->isAdmin()) {
            abort(403, __('You do not have access to this batch.'));
        }
    }

    /**
     * Add students to batch
     */
    public function addStudentsToBatch(Batch $batch, array $studentIds): array
    {
        // Check max_students limit if set
        if ($batch->max_students !== null) {
            $currentStudents = $batch->enrollments()->count();
            $newStudentsCount = count($studentIds);
            $wouldBeTotal = $currentStudents + $newStudentsCount;
            
            if ($wouldBeTotal > $batch->max_students) {
                $validator = validator([], []);
                $validator->errors()->add('student_ids', __('admin.max_students_would_exceed', [
                    'count' => $newStudentsCount,
                    'max' => $batch->max_students,
                    'current' => $currentStudents,
                    'would_be' => $wouldBeTotal,
                ]));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        $added = 0;
        $skipped = 0;

        foreach ($studentIds as $studentId) {
            $user = User::find($studentId);
            
            if (!$user || !$user->isStudent()) {
                $skipped++;
                continue;
            }

            if (!$this->repository->isUserEnrolled($batch, $studentId)) {
                // Check max_students before each enrollment
                if ($batch->max_students !== null) {
                    $currentCount = $batch->enrollments()->count();
                    if ($currentCount >= $batch->max_students) {
                        $validator = validator([], []);
                        $validator->errors()->add('student_ids', __('admin.max_students_exceeded', [
                            'max' => $batch->max_students,
                            'current' => $currentCount,
                        ]));
                        throw new \Illuminate\Validation\ValidationException($validator);
                    }
                }
                
                $this->repository->enrollUser($batch, $studentId);
                $added++;
            } else {
                $skipped++;
            }
        }

        return ['added' => $added, 'skipped' => $skipped];
    }

    /**
     * Remove student from batch
     */
    public function removeStudentFromBatch(Batch $batch, int $userId): bool
    {
        return $this->repository->removeUser($batch, $userId);
    }

    /**
     * Format batch for display
     */
    public function formatBatch(Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'instructor' => $batch->instructor ? [
                'id' => $batch->instructor->id,
                'name' => $batch->instructor->name,
            ] : null,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
            'enrollments_count' => $batch->enrollments_count ?? 0,
        ];
    }

    /**
     * Format batch with students for display
     */
    public function formatBatchWithStudents(Batch $batch): array
    {
        $batch->load(['enrollments.student']);
        
        return [
            ...$this->formatBatch($batch),
            'name' => $batch->name,
            'name_ar' => $batch->name_ar,
            'description' => $batch->description,
            'description_ar' => $batch->description_ar,
            'instructor_id' => $batch->instructor_id,
            'max_students' => $batch->max_students,
            'students' => $batch->enrollments->map(fn($e) => [
                'id' => $e->student->id,
                'name' => $e->student->name,
                'email' => $e->student->email,
                'enrolled_at' => $e->enrolled_at,
                'progress' => $e->progress ?? 0,
                'status' => $e->status,
            ]),
        ];
    }

    /**
     * Validate that no active batch exists before creating new one
     * Returns validation error message if active batch exists
     */
    public function validateNoActiveBatch(Course $course): ?array
    {
        $existingBatch = $this->getActiveBatch($course);
        
        if (!$existingBatch) {
            return null;
        }

        $locale = app()->getLocale();
        $endDate = $existingBatch->end_date 
            ? \Carbon\Carbon::parse($existingBatch->end_date)->locale($locale)->isoFormat('MMM D, YYYY')
            : __('batches.batch_has_no_end_date');

        return [
            'start_date' => __('batches.cannot_create_batch_existing_not_ended', [
                'batch_name' => $existingBatch->translated_name,
                'end_date' => $endDate,
            ]),
        ];
    }

    /**
     * Format course for display
     */
    public function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    /**
     * Get batch data for editing
     */
    public function getBatchForEdit(Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->name,
            'name_ar' => $batch->name_ar,
            'description' => $batch->description,
            'description_ar' => $batch->description_ar,
            'instructor_id' => $batch->instructor_id,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'max_students' => $batch->max_students,
            'is_active' => $batch->is_active,
        ];
    }
}

