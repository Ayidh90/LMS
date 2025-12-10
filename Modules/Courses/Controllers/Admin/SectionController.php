<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreSectionRequest;
use Modules\Courses\Services\SectionService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function __construct(
        private SectionService $sectionService
    ) {}

    public function index(Course $course)
    {
        $sections = $this->sectionService->getByCourse($course->id, ['lessons']);

        return Inertia::render('Admin/Sections/Index', [
            'course' => $this->formatCourse($course),
            'sections' => $sections->map(fn($s) => $this->formatSection($s)),
        ]);
    }

    public function create(Course $course)
    {
        return Inertia::render('Admin/Sections/Create', [
            'course' => $this->formatCourse($course),
        ]);
    }

    public function store(StoreSectionRequest $request, Course $course)
    {
        $this->sectionService->create($course, $request->validated());

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', __('Section created successfully.'));
    }

    public function edit(Course $course, Section $section)
    {
        if (!$this->sectionService->belongsToCourse($section, $course)) {
            abort(404);
        }

        return Inertia::render('Admin/Sections/Edit', [
            'course' => $this->formatCourse($course),
            'section' => $this->formatSection($section),
        ]);
    }

    public function update(Request $request, Course $course, Section $section)
    {
        if (!$this->sectionService->belongsToCourse($section, $course)) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $this->sectionService->update($section, $validated);

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', __('Section updated successfully.'));
    }

    public function destroy(Course $course, Section $section)
    {
        if (!$this->sectionService->belongsToCourse($section, $course)) {
            abort(404);
        }

        $this->sectionService->delete($section);

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', __('Section deleted successfully.'));
    }

    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([
            'section_ids' => 'required|array',
            'section_ids.*' => 'exists:sections,id',
        ]);

        $this->sectionService->reorder($course->id, $validated['section_ids']);

        return back()->with('success', __('Sections reordered successfully.'));
    }

    private function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    private function formatSection(Section $section): array
    {
        return [
            'id' => $section->id,
            'title' => $section->translated_title ?? $section->title,
            'title_ar' => $section->title_ar,
            'description' => $section->translated_description ?? $section->description,
            'description_ar' => $section->description_ar,
            'order' => $section->order,
            'lessons_count' => $section->lessons ? $section->lessons->count() : 0,
            'lessons' => $section->lessons ? $section->lessons->map(fn($l) => [
                'id' => $l->id,
                'title' => $l->translated_title ?? $l->title,
                'type' => $l->type,
                'order' => $l->order,
                'duration_minutes' => $l->duration_minutes,
            ]) : [],
        ];
    }
}

