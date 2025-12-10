<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::where('instructor_id', Auth::id())
            ->with(['course'])
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($b) => $this->formatBatch($b));

        return Inertia::render('Instructor/Batches/Index', [
            'batches' => $batches,
        ]);
    }

    public function show(Batch $batch)
    {
        $this->authorizeAccess($batch);

        $batch->load(['course', 'enrollments.student']);

        return Inertia::render('Instructor/Batches/Show', [
            'batch' => $this->formatBatchWithStudents($batch),
        ]);
    }

    private function authorizeAccess(Batch $batch): void
    {
        $user = Auth::user();
        
        if ($batch->instructor_id !== $user->id && !$user->isAdmin()) {
            abort(403, __('You do not have access to this batch.'));
        }
    }

    private function formatBatch(Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'course' => [
                'id' => $batch->course->id,
                'title' => $batch->course->translated_title,
                'slug' => $batch->course->slug,
            ],
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
            'enrollments_count' => $batch->enrollments_count,
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
