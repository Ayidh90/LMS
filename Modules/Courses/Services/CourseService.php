<?php

namespace Modules\Courses\Services;

use Modules\Courses\Repositories\CourseRepository;
use Modules\Courses\Models\Course;
use App\Services\ImageService;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    public function __construct(
        private CourseRepository $repository,
        private ImageService $imageService
    ) {}

    /**
     * Get paginated courses with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedCourses(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $relations = []; // Removed 'instructor' and 'category' - instructors are assigned to batches, categories removed
        $courses = $this->repository->getPaginated($perPage, $relations, $filters);

        // Ensure translations, slug, and thumbnail URLs are available
        foreach ($courses->items() as $course) {
            // The thumbnail_url accessor will automatically convert paths to URLs
            // Ensure slug and translated fields are available (they're already appended)
            $course->makeVisible(['slug', 'thumbnail_url', 'translated_title', 'translated_description']);
            
            // Set thumbnail to thumbnail_url for frontend consistency
            $course->thumbnail = $course->thumbnail_url;
        }

        return $courses;
    }

    /**
     * Get course by ID
     *
     * @param int $id
     * @return Course|null
     */
    public function getCourseById(int $id): ?Course
    {
        $course = $this->repository->findById($id, []); // Removed 'instructor' and 'category'
        
        if ($course) {
            // Ensure thumbnail_url, slug, and translated fields are visible (they're already appended)
            $course->makeVisible(['slug', 'thumbnail_url', 'translated_title', 'translated_description']);
            
            // Set thumbnail to thumbnail_url for frontend
            $course->thumbnail = $course->thumbnail_url;
            
            // Also translate lessons (they're already appended)
            if ($course->lessons) {
                foreach ($course->lessons as $lesson) {
                    $lesson->makeVisible(['translated_title', 'translated_description', 'translated_content']);
                }
            }
        }

        return $course;
    }

    /**
     * Get course by slug
     *
     * @param string $slug
     * @return Course|null
     */
    public function getCourseBySlug(string $slug): ?Course
    {
        $course = $this->repository->findBySlug($slug, ['lessons']); // Removed 'instructor' and 'category'
        
        if ($course) {
            $course->thumbnail = $this->imageService->getUrl($course->thumbnail);
        }

        return $course;
    }

    /**
     * Create a new course
     *
     * @param array $data
     * @param UploadedFile|null $thumbnailFile
     * @return Course
     */
    public function createCourse(array $data, ?UploadedFile $thumbnailFile = null): Course
    {
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle thumbnail upload
        if ($thumbnailFile) {
            // Use slug-based filename for easy replacement
            $filename = $data['slug'] . '.' . $thumbnailFile->getClientOriginalExtension();
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'courses', null, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            // Download from URL if provided - use slug-based filename
            $slug = $data['slug'] ?? Str::slug($data['title']);
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'courses', $filename);
            unset($data['thumbnail_url']);
        } elseif (isset($data['thumbnail']) && filter_var($data['thumbnail'], FILTER_VALIDATE_URL)) {
            // Download from URL if thumbnail field contains URL
            $slug = $data['slug'] ?? Str::slug($data['title']);
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail'], 'courses', $filename);
        } elseif (isset($data['thumbnail']) && empty($data['thumbnail'])) {
            $data['thumbnail'] = null;
        } else {
            unset($data['thumbnail']);
        }

        // Ensure translations are set
        $data['title_ar'] = $data['title_ar'] ?? null;
        $data['description_ar'] = $data['description_ar'] ?? null;

        return $this->repository->create($data);
    }

    /**
     * Update a course
     *
     * @param Course $course
     * @param array $data
     * @param UploadedFile|null $thumbnailFile
     * @return bool
     */
    public function updateCourse(Course $course, array $data, ?UploadedFile $thumbnailFile = null): bool
    {
        // Handle thumbnail upload
        if ($thumbnailFile) {
            // Use slug-based filename to replace old thumbnail
            $slug = $course->slug;
            $filename = $slug . '.' . $thumbnailFile->getClientOriginalExtension();
            // Delete old thumbnail and upload new one with same filename (replaces it)
            $data['thumbnail'] = $this->imageService->upload($thumbnailFile, 'courses', $course->thumbnail, $filename);
        } elseif (isset($data['thumbnail_url']) && filter_var($data['thumbnail_url'], FILTER_VALIDATE_URL)) {
            // Download from URL if provided - use slug-based filename
            $slug = $course->slug;
            $filename = $slug . '.jpg';
            $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail_url'], 'courses', $filename, $course->thumbnail);
            unset($data['thumbnail_url']);
        } elseif (isset($data['thumbnail'])) {
            if (filter_var($data['thumbnail'], FILTER_VALIDATE_URL)) {
                // Download from URL if thumbnail field contains URL - use slug-based filename
                $slug = $course->slug;
                $filename = $slug . '.jpg';
                $data['thumbnail'] = $this->imageService->downloadFromUrl($data['thumbnail'], 'courses', $filename, $course->thumbnail);
            } elseif (empty($data['thumbnail']) && $course->thumbnail) {
                // Delete thumbnail if empty
                $this->imageService->delete($course->thumbnail);
                $data['thumbnail'] = null;
            } else {
                // Keep existing thumbnail if not changed
                unset($data['thumbnail']);
            }
        }

        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $course->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Ensure translations are set
        if (isset($data['title_ar']) && empty($data['title_ar'])) {
            $data['title_ar'] = null;
        }
        if (isset($data['description_ar']) && empty($data['description_ar'])) {
            $data['description_ar'] = null;
        }

        return $this->repository->update($course, $data);
    }

    /**
     * Delete a course
     *
     * @param Course $course
     * @return bool
     */
    public function deleteCourse(Course $course): bool
    {
        // Delete associated thumbnail
        $this->imageService->delete($course->thumbnail);

        return $this->repository->delete($course);
    }

    /**
     * Get courses for a specific user role
     *
     * @param int|null $userId
     * @param string|null $role
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getCoursesForUser(?int $userId, ?string $role, int $perPage = 12, array $additionalFilters = []): LengthAwarePaginator
    {
        $userFilters = [];

        if ($role === 'admin') {
            // Admin sees all courses
            $userFilters = [];
        } elseif ($role === 'instructor' && $userId) {
            // Instructor sees only courses they teach through batches
            $userFilters['instructor_id'] = $userId;
        } else {
            // Students and guests see only published courses
            $userFilters['is_published'] = true;
        }

        // Merge user-specific filters with provided filters
        $mergedFilters = array_merge($userFilters, $additionalFilters);
        return $this->getPaginatedCourses($mergedFilters, $perPage);
    }
}

