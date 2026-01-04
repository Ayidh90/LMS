<?php

namespace Modules\Courses\Services;

use Modules\Courses\Models\Course;
use App\Models\Enrollment;
use Modules\Courses\Repositories\CourseRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseExportService
{
    private const EXPORT_FORMAT_CSV = 'csv';

    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    /**
     * Check if request is for export
     */
    public function isExportRequest(Request $request): bool
    {
        return $request->has('export') && $request->get('export') === self::EXPORT_FORMAT_CSV;
    }

    /**
     * Export courses to CSV
     */
    public function exportCourses(array $filters): StreamedResponse
    {
        $courses = $this->getCoursesForExport($filters);
        $filename = 'courses_' . date('Y-m-d_His') . '.csv';

        return response()->stream(
            fn() => $this->generateCsvContent($courses),
            200,
            $this->getCsvHeaders($filename)
        );
    }

    /**
     * Build filters from request
     */
    public function buildFilters(Request $request): array
    {
        $filters = [];

        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }

        $status = $request->query('status');
        if ($status === 'published') {
            $filters['is_published'] = true;
        } elseif ($status === 'draft') {
            $filters['is_published'] = false;
        }

        if ($level = $request->query('level')) {
            $filters['level'] = $level;
        }

        return $filters;
    }

    /**
     * Get courses for export
     */
    private function getCoursesForExport(array $filters)
    {
        $query = Course::query();

        $this->applySearchFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applyLevelFilter($query, $filters);
        $this->addEnrollmentsCount($query);

        return $query->latest()->get();
    }

    /**
     * Apply search filter to query
     */
    private function applySearchFilter($query, array $filters): void
    {
        if (!isset($filters['search'])) {
            return;
        }

        $search = $filters['search'];
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('title_ar', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('description_ar', 'like', "%{$search}%");
        });
    }

    /**
     * Apply status filter to query
     */
    private function applyStatusFilter($query, array $filters): void
    {
        if (isset($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }
    }

    /**
     * Apply level filter to query
     */
    private function applyLevelFilter($query, array $filters): void
    {
        if (isset($filters['level'])) {
            $query->where('level', $filters['level']);
        }
    }

    /**
     * Add enrollments count to query
     */
    private function addEnrollmentsCount($query): void
    {
        $query->addSelect([
            'enrollments_count' => Enrollment::selectRaw('COALESCE(COUNT(*), 0)')
                ->join('batches', 'enrollments.batch_id', '=', 'batches.id')
                ->whereColumn('batches.course_id', 'courses.id')
        ]);
    }

    /**
     * Generate CSV content
     */
    private function generateCsvContent($courses): void
    {
        $file = fopen('php://output', 'w');

        fputcsv($file, $this->getCsvColumnHeaders());

        foreach ($courses as $course) {
            fputcsv($file, $this->formatCourseForCsv($course));
        }

        fclose($file);
    }

    /**
     * Get CSV HTTP headers
     */
    private function getCsvHeaders(string $filename): array
    {
        return [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
    }

    /**
     * Get CSV column headers
     */
    private function getCsvColumnHeaders(): array
    {
        return [
            'ID',
            'Title',
            'Title (Arabic)',
            'Level',
            'Status',
            'Duration (Hours)',
            'Price',
            'Students Count',
            'Created At',
        ];
    }

    /**
     * Format course data for CSV
     */
    private function formatCourseForCsv(Course $course): array
    {
        return [
            $course->id,
            $course->title ?? '',
            $course->title_ar ?? '',
            $course->level ?? '',
            $course->is_published ? 'Published' : 'Draft',
            $course->duration_hours ?? 0,
            $course->price ?? 0,
            $course->enrollments_count ?? 0,
            $course->created_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }
}

