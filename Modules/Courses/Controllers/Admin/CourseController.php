<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BatchService;
use App\Services\TrackService;
use Modules\Courses\Models\Course;
use Modules\Courses\Requests\StoreCourseRequest;
use Modules\Courses\Requests\UpdateCourseRequest;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Services\CourseStatisticsService;
use Modules\Courses\Services\CourseExportService;
use Modules\Courses\Services\CoursePresentationService;
use Modules\Courses\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CourseController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private CourseService $courseService,
        private CourseStatisticsService $statisticsService,
        private CourseExportService $exportService,
        private CoursePresentationService $presentationService,
        private SectionService $sectionService,
        private TrackService $trackService,
        private BatchService $batchService
    ) {}

    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        if ($this->exportService->isExportRequest($request)) {
            $filters = $this->exportService->buildFilters($request);
            return $this->exportService->exportCourses($filters);
        }

        $filters = $this->exportService->buildFilters($request);
        $courses = $this->courseService->getPaginatedCourses($filters, self::PER_PAGE);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'level']),
        ]);
    }

    /**
     * Show the form for creating a new course
     */
    public function create(Request $request)
    {
        $tracks = $this->trackService->getPaginatedTracks([], 100)->items();
        $selectedTrackId = $request->input('track_id');
        
        return Inertia::render('Admin/Courses/Create', [
            'tracks' => $tracks,
            'selectedTrackId' => $selectedTrackId,
        ]);
    }

    /**
     * Store a newly created course
     */
    public function store(StoreCourseRequest $request)
    {
        $this->courseService->createCourse(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()->back()
            ->with('success', __('messages.courses.created_successfully'));
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        try {
            $this->presentationService->loadCourseRelations($course);
            $this->presentationService->prepareCourseForDisplay($course);

            $statistics = $this->statisticsService->getCourseStatistics($course);
            $sections = $this->sectionService->getByCourse($course->id)->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'title_ar' => $s->title_ar,
                'translated_title' => $s->translated_title ?? $s->title,
            ]);
            $instructors = $this->batchService->getActiveInstructors();
            $existingBatch = $this->batchService->getActiveBatch($course);

            return Inertia::render('Admin/Courses/Show', [
                'course' => $course,
                'statistics' => $statistics,
                'sections' => $sections,
                'instructors' => $instructors,
                'lessonTypes' => $this->presentationService->getLessonTypes(),
                'questionTypes' => $this->presentationService->getQuestionTypes(),
                'existingBatch' => $existingBatch ? [
                    'id' => $existingBatch->id,
                    'name' => $existingBatch->translated_name,
                    'end_date' => $existingBatch->end_date,
                ] : null,
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

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);
        $tracks = $this->trackService->getPaginatedTracks([], 100)->items();

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'tracks' => $tracks,
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

        return redirect()->back()
            ->with('success', __('messages.courses.updated_successfully'));
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);

        return redirect()->back()
            ->with('success', __('messages.courses.deleted_successfully'));
    }

}
