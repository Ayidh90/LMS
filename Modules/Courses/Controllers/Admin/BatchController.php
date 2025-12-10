<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use App\Models\Enrollment;
use Modules\Courses\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function index(Course $course)
    {
        $batches = $course->batches()
            ->with(['instructor:id,name,email'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($b) => $this->formatBatch($b));

        return Inertia::render('Admin/Batches/Index', [
            'course' => $this->formatCourse($course),
            'batches' => $batches,
        ]);
    }

    public function create(Course $course)
    {
        return Inertia::render('Admin/Batches/Create', [
            'course' => $this->formatCourse($course),
            'instructors' => $this->getInstructors(),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $validated = $this->validateBatch($request);
        $course->batches()->create($validated);

        return redirect()->route('admin.courses.batches.index', $course)
            ->with('success', __('Batch created successfully.'));
    }

    public function show(Course $course, Batch $batch)
    {
        $batch->load(['instructor:id,name,email', 'enrollments.student']);
        $availableStudents = $this->getAvailableStudents($batch);

        return Inertia::render('Admin/Batches/Show', [
            'course' => $this->formatCourse($course),
            'batch' => $this->formatBatchWithStudents($batch),
            'availableStudents' => $availableStudents,
        ]);
    }

    public function edit(Course $course, Batch $batch)
    {
        return Inertia::render('Admin/Batches/Edit', [
            'course' => $this->formatCourse($course),
            'batch' => $batch,
            'instructors' => $this->getInstructors(),
        ]);
    }

    public function update(Request $request, Course $course, Batch $batch)
    {
        $validated = $this->validateBatch($request);
        $batch->update($validated);

        return redirect()->route('admin.courses.batches.index', $course)
            ->with('success', __('Batch updated successfully.'));
    }

    public function destroy(Course $course, Batch $batch)
    {
        $batch->delete();

        return redirect()->route('admin.courses.batches.index', $course)
            ->with('success', __('Batch deleted successfully.'));
    }

    public function addStudents(Request $request, Course $course, Batch $batch)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($validated['student_ids'] as $studentId) {
            Enrollment::firstOrCreate(
                ['batch_id' => $batch->id, 'student_id' => $studentId],
                ['enrolled_at' => now(), 'status' => 'active', 'progress' => 0]
            );
        }

        return redirect()->back()->with('success', __('Students added successfully.'));
    }

    public function removeStudent(Course $course, Batch $batch, User $student)
    {
        Enrollment::where('batch_id', $batch->id)
            ->where('student_id', $student->id)
            ->delete();

        return redirect()->back()->with('success', __('Student removed successfully.'));
    }

    private function validateBatch(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
    }

    private function getInstructors()
    {
        return User::where('role', 'instructor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    private function getAvailableStudents(Batch $batch)
    {
        $enrolledIds = $batch->enrollments->pluck('student_id');
        
        return User::where('role', 'student')
            ->whereNotIn('id', $enrolledIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    private function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    private function formatBatch(Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'instructor' => $batch->instructor ? [
                'id' => $batch->instructor->id,
                'name' => $batch->instructor->name,
            ] : null,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
            'enrollments_count' => $batch->enrollments_count ?? 0,
        ];
    }

    private function formatBatchWithStudents(Batch $batch): array
    {
        return [
            ...$this->formatBatch($batch),
            'students' => $batch->enrollments->map(fn($e) => [
                'id' => $e->student->id,
                'name' => $e->student->name,
                'email' => $e->student->email,
                'enrolled_at' => $e->enrolled_at,
                'progress' => $e->progress ?? 0,
                'status' => $e->status,
            ]),
        ];
    }
}
