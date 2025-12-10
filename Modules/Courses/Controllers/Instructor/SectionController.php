<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreSectionRequest;
use Modules\Courses\Services\SectionService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function __construct(
        private SectionService $sectionService
    ) {}

    public function index(Course $course)
    {
        Gate::authorize('viewLessons', $course);

        $sections = $this->sectionService->getByCourse($course->id, ['lessons']);

        return Inertia::render('Instructor/Sections/Index', [
            'course' => $this->formatCourse($course),
            'sections' => $sections->map(fn($s) => $this->formatSection($s)),
        ]);
    }

    public function store(StoreSectionRequest $request, Course $course)
    {
        Gate::authorize('createLesson', $course);

        $this->sectionService->create($course, $request->validated());

        return back()->with('success', __('Section created successfully.'));
    }

    public function update(Request $request, Course $course, Section $section)
    {
        Gate::authorize('updateLesson', $course);

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

        return back()->with('success', __('Section updated successfully.'));
    }

    public function destroy(Course $course, Section $section)
    {
        Gate::authorize('deleteLesson', $course);

        if (!$this->sectionService->belongsToCourse($section, $course)) {
            abort(404);
        }

        $this->sectionService->delete($section);

        return back()->with('success', __('Section deleted successfully.'));
    }

    public function reorder(Request $request, Course $course)
    {
        Gate::authorize('updateLesson', $course);

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
            'order' => $section->order,
            'lessons' => $section->lessons->map(fn($l) => [
                'id' => $l->id,
                'title' => $l->translated_title ?? $l->title,
                'type' => $l->type,
                'order' => $l->order,
                'duration_minutes' => $l->duration_minutes,
            ]),
        ];
    }
}
