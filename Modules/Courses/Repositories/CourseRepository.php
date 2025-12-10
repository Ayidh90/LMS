<?php

namespace Modules\Courses\Repositories;

use Modules\Courses\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseRepository
{
    /**
     * Get all courses with relations
     *
     * @param array $relations
     * @return Collection
     */
    public function getAll(array $relations = []): Collection
    {
        $query = Course::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->latest()->get();
    }

    /**
     * Get paginated courses
     *
     * @param int $perPage
     * @param array $relations
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 12, array $relations = [], array $filters = []): LengthAwarePaginator
    {
        $query = Course::query();

        // Apply filters
        // Note: instructor_id filter removed - instructors are now assigned to batches, not courses directly
        if (isset($filters['instructor_id'])) {
            // Filter courses by instructor through batches
            $query->whereHas('batches', function($q) use ($filters) {
                $q->where('instructor_id', $filters['instructor_id']);
            });
        }

        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%");
            });
        }

        // Free courses filter
        if (isset($filters['free']) && $filters['free']) {
            $query->where('price', 0);
        }

        // Price range filter
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Instructor filter - already handled above

        // Sort
        $sortBy = $filters['sort'] ?? 'newest';
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        // Load relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find course by ID
     *
     * @param int $id
     * @param array $relations
     * @return Course|null
     */
    public function findById(int $id, array $relations = []): ?Course
    {
        $query = Course::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * Find course by slug
     *
     * @param string $slug
     * @param array $relations
     * @return Course|null
     */
    public function findBySlug(string $slug, array $relations = []): ?Course
    {
        $query = Course::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->where('slug', $slug)->first();
    }

    /**
     * Create a new course
     *
     * @param array $data
     * @return Course
     */
    public function create(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Update a course
     *
     * @param Course $course
     * @param array $data
     * @return bool
     */
    public function update(Course $course, array $data): bool
    {
        return $course->update($data);
    }

    /**
     * Delete a course
     *
     * @param Course $course
     * @return bool
     */
    public function delete(Course $course): bool
    {
        return $course->delete();
    }

    /**
     * Get courses by instructor ID (through batches)
     *
     * @param int $instructorId
     * @param array $relations
     * @return Collection
     */
    public function getByInstructor(int $instructorId, array $relations = []): Collection
    {
        $query = Course::whereHas('batches', function($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        });

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->latest()->get();
    }

    /**
     * Get published courses
     *
     * @param array $relations
     * @return Collection
     */
    public function getPublished(array $relations = []): Collection
    {
        $query = Course::where('is_published', true);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->latest()->get();
    }
}

