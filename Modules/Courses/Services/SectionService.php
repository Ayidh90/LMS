<?php

namespace Modules\Courses\Services;

use Modules\Courses\Repositories\SectionRepository;
use Modules\Courses\Models\Section;
use Modules\Courses\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class SectionService
{
    public function __construct(
        private SectionRepository $repository
    ) {}

    public function getByCourse(int $courseId, array $relations = ['lessons']): Collection
    {
        return $this->repository->getByCourse($courseId, $relations);
    }

    public function getById(int $id, array $relations = []): ?Section
    {
        return $this->repository->findById($id, $relations);
    }

    public function create(Course $course, array $data): Section
    {
        $data['course_id'] = $course->id;
        $data['order'] = $data['order'] ?? $this->repository->getNextOrder($course->id);
        
        return $this->repository->create($data);
    }

    public function update(Section $section, array $data): bool
    {
        return $this->repository->update($section, $data);
    }

    public function delete(Section $section): bool
    {
        return $this->repository->delete($section);
    }

    public function reorder(int $courseId, array $orderedIds): void
    {
        $this->repository->reorder($courseId, $orderedIds);
    }

    public function belongsToCourse(Section $section, Course $course): bool
    {
        return $section->course_id === $course->id;
    }
}

