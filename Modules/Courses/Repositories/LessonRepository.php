<?php

namespace Modules\Courses\Repositories;

use Modules\Courses\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LessonRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Lesson());
    }

    public function getByCourse(int $courseId, array $relations = []): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->with($relations)
            ->orderBy('order')
            ->get();
    }

    public function getBySection(int $sectionId, array $relations = []): Collection
    {
        return Lesson::where('section_id', $sectionId)
            ->with($relations)
            ->orderBy('order')
            ->get();
    }

    public function getByType(int $courseId, string $type, array $relations = []): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->where('type', $type)
            ->with($relations)
            ->orderBy('order')
            ->get();
    }

    public function getNextOrder(int $courseId, ?int $sectionId = null): int
    {
        $query = Lesson::where('course_id', $courseId);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        return ($query->max('order') ?? 0) + 1;
    }

    public function reorder(int $courseId, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            Lesson::where('id', $id)
                ->where('course_id', $courseId)
                ->update(['order' => $order + 1]);
        }
    }

    public function countByCourse(int $courseId): int
    {
        return Lesson::where('course_id', $courseId)->count();
    }

    public function countByType(int $courseId, string $type): int
    {
        return Lesson::where('course_id', $courseId)
            ->where('type', $type)
            ->count();
    }
}
