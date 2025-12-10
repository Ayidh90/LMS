<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreCourseRequest;
use Modules\Courses\Requests\UpdateCourseRequest;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function index(Request $request)
    {
        $filters = $this->buildFilters($request);
        $courses = $this->courseService->getPaginatedCourses($filters, 15);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Courses/Create');
    }

    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->createCourse(
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()->route('admin.courses.index')
            ->with('success', __('messages.courses.created_successfully'));
    }

    public function show(Course $course)
    {
        $course->load([
            'sections' => function($query) {
                $query->orderBy('order');
            },
            'sections.lessons' => function($query) {
                $query->orderBy('order');
            },
            'lessons' => function($query) {
                $query->orderBy('order');
            },
            'batches' => function($query) {
                $query->with('instructor:id,name,email');
            },
        ]);

        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        // Get course statistics
        $statistics = $this->getCourseStatistics($course);

        return Inertia::render('Admin/Courses/Show', [
            'course' => $course,
            'statistics' => $statistics,
        ]);
    }

    public function edit(Course $course)
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->courseService->updateCourse(
            $course,
            $request->validated(),
            $request->file('thumbnail')
        );

        return redirect()->route('admin.courses.index')
            ->with('success', __('messages.courses.updated_successfully'));
    }

    public function destroy(Course $course)
    {
        $this->courseService->deleteCourse($course);

        return redirect()->route('admin.courses.index')
            ->with('success', __('messages.courses.deleted_successfully'));
    }

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
        
        return $filters;
    }


    private function getCourseStatistics(Course $course): array
    {
        $batches = $course->batches;
        $totalEnrollments = \App\Models\Enrollment::whereIn('batch_id', $batches->pluck('id'))->count();
        $totalStudents = \App\Models\Enrollment::whereIn('batch_id', $batches->pluck('id'))->distinct('student_id')->count();
        $totalLessons = $course->lessons()->count();
        $totalSections = $course->sections()->count();
        $completedLessons = \App\Models\LessonCompletion::whereIn('lesson_id', $course->lessons()->pluck('id'))->distinct('student_id')->count();

        return [
            'total_batches' => $batches->count(),
            'total_enrollments' => $totalEnrollments,
            'total_students' => $totalStudents,
            'total_lessons' => $totalLessons,
            'total_sections' => $totalSections,
            'completed_lessons' => $completedLessons,
            'completion_rate' => $totalLessons > 0 ? round(($completedLessons / ($totalStudents * $totalLessons)) * 100, 2) : 0,
        ];
    }
}
