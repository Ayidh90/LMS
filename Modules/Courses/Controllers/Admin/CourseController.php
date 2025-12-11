<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Question;
use Modules\Courses\Repositories\CourseRepository;
use Modules\Courses\Requests\StoreCourseRequest;
use Modules\Courses\Requests\UpdateCourseRequest;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseController extends Controller
{
    private const PER_PAGE = 15;
    private const EXPORT_FORMAT_CSV = 'csv';

    public function __construct(
        private CourseService $courseService,
        private CourseRepository $courseRepository,
        private SectionService $sectionService
    ) {}

    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        if ($this->isExportRequest($request)) {
            return $this->exportCourses($request);
        }

        $filters = $this->buildFilters($request);
        $courses = $this->courseService->getPaginatedCourses($filters, self::PER_PAGE);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'level']),
        ]);
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        return Inertia::render('Admin/Courses/Create');
    }

    /**
     * Store a newly created course
     */
    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->createCourse(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.courses.index')
            ->with('success', __('messages.courses.created_successfully'));
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        try {
            $this->loadCourseRelations($course);
            $this->makeTranslatedFieldsVisible($course);

            $statistics = $this->getCourseStatistics($course);
            
            // Get sections for modals
            $sections = $this->sectionService->getByCourse($course->id)->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
                'title_ar' => $s->title_ar,
            ]);
            
            // Get instructors for batch modals
            $instructors = User::where('role', 'instructor')
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            return Inertia::render('Admin/Courses/Show', [
                'course' => $course,
                'statistics' => $statistics,
                'sections' => $sections,
                'instructors' => $instructors,
                'lessonTypes' => $this->getLessonTypes(),
                'questionTypes' => $this->getQuestionTypes(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in CourseController@show: ' . $e->getMessage(), [
                'course_id' => $course->id,
                'course_slug' => $course->slug,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.courses.index')
                ->with('error', __('messages.courses.show_error') ?? 'Error loading course details.');
        }
    }
    
    private function getLessonTypes(): array
    {
        return [
            ['value' => 'text', 'label' => __('lessons.types.text')],
            ['value' => 'youtube_video', 'label' => __('lessons.types.youtube_video')],
            ['value' => 'google_drive_video', 'label' => __('lessons.types.google_drive_video')],
            ['value' => 'video_file', 'label' => __('lessons.types.video_file')],
            ['value' => 'image', 'label' => __('lessons.types.image')],
            ['value' => 'document_file', 'label' => __('lessons.types.document_file')],
            ['value' => 'embed_frame', 'label' => __('lessons.types.embed_frame')],
            ['value' => 'assignment', 'label' => __('lessons.types.assignment')],
            ['value' => 'test', 'label' => __('lessons.types.test')],
        ];
    }
    
    private function getQuestionTypes(): array
    {
        return [
            ['value' => 'multiple_choice', 'label' => __('Multiple Choice')],
            ['value' => 'true_false', 'label' => __('True/False')],
            ['value' => 'short_answer', 'label' => __('Short Answer')],
            ['value' => 'essay', 'label' => __('Essay')],
        ];
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
        ]);
    }

    /**
     * Update the specified course
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->courseService->updateCourse(
            $course,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.courses.index')
            ->with('success', __('messages.courses.updated_successfully'));
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', __('messages.courses.deleted_successfully'));
    }

    /**
     * Build filters from request
     */
    private function buildFilters(Request $request): array
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
     * Load course relations for show page
     */
    private function loadCourseRelations(Course $course): void
    {
        $course->load([
            'sections' => fn($query) => $query->orderBy('order'),
            'sections.lessons' => fn($query) => $query->orderBy('order')->with(['questions.answers']),
            'lessons' => fn($query) => $query->orderBy('order')->with(['questions.answers']),
            'batches' => fn($query) => $query->with('instructor:id,name,email'),
        ]);
    }

    /**
     * Make translated fields visible for course and its relations
     */
    private function makeTranslatedFieldsVisible(Course $course): void
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        $this->makeSectionsVisible($course);
        $this->makeLessonsVisible($course->lessons);
    }

    /**
     * Make translated fields visible for sections
     */
    private function makeSectionsVisible(Course $course): void
    {
        if (!$course->sections) {
            return;
        }

        foreach ($course->sections as $section) {
            $section->makeVisible(['translated_title', 'translated_description']);

            if ($section->lessons) {
                $this->makeLessonsVisible($section->lessons);
            }
        }
    }

    /**
     * Make translated fields visible for lessons
     */
    private function makeLessonsVisible($lessons): void
    {
        if (!$lessons) {
            return;
        }

        foreach ($lessons as $lesson) {
            $lesson->makeVisible(['translated_title', 'translated_description', 'translated_content']);

            if ($lesson->questions) {
                foreach ($lesson->questions as $question) {
                    $question->makeVisible(['translated_question', 'translated_explanation']);
                }
            }
        }
    }

    /**
     * Get course statistics
     */
    private function getCourseStatistics(Course $course): array
    {
        $batches = $course->batches;
        $batchIds = $batches->pluck('id')->toArray();
        $lessonIds = $course->lessons()->pluck('id')->toArray();

        // Handle empty arrays to prevent SQL errors
        $enrollments = Enrollment::query();
        if (!empty($batchIds)) {
            $enrollments->whereIn('batch_id', $batchIds);
        } else {
            $enrollments->whereRaw('1 = 0'); // Return empty result set
        }
        
        $totalEnrollments = $enrollments->count();
        $totalStudents = $enrollments->distinct('student_id')->count();

        $totalLessons = $course->lessons()->count();
        $totalSections = $course->sections()->count();
        
        $totalQuestions = 0;
        if (!empty($lessonIds)) {
            $totalQuestions = Question::whereIn('lesson_id', $lessonIds)->count();
        }
        
        $completedLessons = 0;
        if (!empty($lessonIds)) {
            $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', $lessonIds)
                ->distinct('student_id')
                ->count();
        }

        $completionRate = $this->calculateCompletionRate(
            $completedLessons,
            $totalStudents,
            $totalLessons
        );

        return [
            'total_batches' => $batches->count(),
            'total_enrollments' => $totalEnrollments,
            'total_students' => $totalStudents,
            'total_lessons' => $totalLessons,
            'total_sections' => $totalSections,
            'total_questions' => $totalQuestions,
            'completed_lessons' => $completedLessons,
            'completion_rate' => $completionRate,
        ];
    }

    /**
     * Calculate completion rate
     */
    private function calculateCompletionRate(int $completedLessons, int $totalStudents, int $totalLessons): float
    {
        if ($totalLessons === 0) {
            return 0;
        }

        $totalPossibleCompletions = $totalStudents * $totalLessons;
        if ($totalPossibleCompletions === 0) {
            return 0;
        }

        return round(($completedLessons / $totalPossibleCompletions) * 100, 2);
    }

    /**
     * Check if request is for export
     */
    private function isExportRequest(Request $request): bool
    {
        return $request->has('export') && $request->get('export') === self::EXPORT_FORMAT_CSV;
    }

    /**
     * Export courses to CSV
     */
    private function exportCourses(Request $request): StreamedResponse
    {
        $filters = $this->buildFilters($request);
        $courses = $this->getCoursesForExport($filters);

        $filename = 'courses_' . date('Y-m-d_His') . '.csv';

        return response()->stream(
            fn() => $this->generateCsvContent($courses),
            200,
            $this->getCsvHeaders($filename)
        );
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
