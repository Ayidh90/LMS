<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\BatchService;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function __construct(
        private BatchService $batchService
    ) {}

    public function index()
    {
        $batches = $this->batchService->getBatchesForInstructor(Auth::id());

        return Inertia::render('Instructor/Batches/Index', [
            'batches' => $batches,
        ]);
    }

    public function show(Batch $batch)
    {
        $this->batchService->authorizeBatchAccess($batch, Auth::user());

        $batch->load(['course', 'enrollments.student']);

        return Inertia::render('Instructor/Batches/Show', [
            'batch' => $this->batchService->formatBatchWithStudents($batch),
        ]);
    }
}
