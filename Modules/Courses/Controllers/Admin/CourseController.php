<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreCourseRequest;
use Modules\Courses\Requests\UpdateCourseRequest;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Models\Course;
use App\Models\Category;
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
        return Inertia::render('Admin/Courses/Create', [
            'categories' => $this->getActiveCategories(),
        ]);
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

    public function edit(Course $course)
    {
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'categories' => $this->getActiveCategories(),
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

    private function getActiveCategories(): array
    {
        return Category::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->translated_name ?: $c->name,
                'name_ar' => $c->name_ar,
            ])
            ->toArray();
    }
}
