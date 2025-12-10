<?php

namespace Modules\Courses\Repositories;

use Modules\Courses\Models\Section;
use Illuminate\Database\Eloquent\Collection;

class SectionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Section());
    }

    public function getByCourse(int $courseId, array $relations = []): Collection
    {
        return Section::where('course_id', $courseId)
            ->with($relations)
            ->orderBy('order')
            ->get();
    }

    public function getNextOrder(int $courseId): int
    {
        return (Section::where('course_id', $courseId)->max('order') ?? 0) + 1;
    }

    public function reorder(int $courseId, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            Section::where('id', $id)
                ->where('course_id', $courseId)
                ->update(['order' => $order + 1]);
        }
    }
}

