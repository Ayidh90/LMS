<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBatchRequest;
use App\Http\Requests\Admin\UpdateBatchRequest;
use App\Http\Requests\Admin\AddStudentsToBatchRequest;
use App\Models\Batch;
use App\Models\User;
use App\Services\BatchService;
use App\Services\ExcelImportService;
use App\Services\ExcelTemplateService;
use Modules\Courses\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BatchController extends Controller
{
    public function __construct(
        private BatchService $batchService,
        private ExcelImportService $excelImportService,
        private ExcelTemplateService $excelTemplateService
    ) {}

    public function index(Course $course)
    {
        $batches = $this->batchService->getBatchesForCourse($course)
            ->map(fn($b) => $this->batchService->formatBatch($b));

        return Inertia::render('Admin/Batches/Index', [
            'course' => $this->batchService->formatCourse($course),
            'batches' => $batches,
            'instructors' => $this->batchService->getActiveInstructors(),
        ]);
    }

    public function create(Course $course)
    {
        $existingBatch = $this->batchService->getActiveBatch($course);
        
        return Inertia::render('Admin/Batches/Create', [
            'course' => $this->batchService->formatCourse($course),
            'instructors' => $this->batchService->getActiveInstructors(),
            'existingBatch' => $existingBatch ? [
                'id' => $existingBatch->id,
                'name' => $existingBatch->translated_name,
                'end_date' => $existingBatch->end_date,
            ] : null,
        ]);
    }

    public function store(StoreBatchRequest $request, Course $course)
    {
        $validationError = $this->batchService->validateNoActiveBatch($course);
        
        if ($validationError) {
            return redirect()->back()
                ->withErrors($validationError)
                ->withInput();
        }
        
        $this->batchService->createBatch($course, $request->validated());

        return redirect()->back()
            ->with('success', __('batches.created_successfully'));
    }

    public function show(Course $course, Batch $batch)
    {
        $batch->load(['instructor:id,name,email', 'enrollments.student']);
        $availableStudents = $this->batchService->getAvailableStudents($batch);

        return Inertia::render('Admin/Batches/Show', [
            'course' => $this->batchService->formatCourse($course),
            'batch' => $this->batchService->formatBatchWithStudents($batch),
            'availableStudents' => $availableStudents,
            'instructors' => $this->batchService->getActiveInstructors(),
        ]);
    }

    public function edit(Course $course, Batch $batch)
    {
        // Return batch data for editing (used by modal)
        return redirect()->back()->with('edit_batch', $this->batchService->getBatchForEdit($batch));
    }

    public function update(UpdateBatchRequest $request, Course $course, Batch $batch)
    {
        $this->batchService->updateBatch($batch, $request->validated());

        return redirect()->back()
            ->with('success', __('batches.updated_successfully'));
    }

    public function destroy(Course $course, Batch $batch)
    {
        $this->batchService->deleteBatch($batch);

        return redirect()->back()
            ->with('success', __('batches.deleted_successfully'));
    }

    public function addStudents(AddStudentsToBatchRequest $request, Course $course, Batch $batch)
    {
        $result = $this->batchService->addStudentsToBatch($batch, $request->validated()['student_ids']);

        if ($result['skipped'] > 0) {
            return redirect()->back()
                ->with('warning', __('Some users were skipped because they do not have student role.'));
        }

        return redirect()->back()
            ->with('success', __('Students added successfully.'));
    }

    public function removeStudent(Course $course, Batch $batch, User $student)
    {
        $this->batchService->removeStudentFromBatch($batch, $student->id);

        return redirect()->back()
            ->with('success', __('Student removed successfully.'));
    }

    public function downloadTemplate(Request $request, Course $course, Batch $batch)
    {
        try {
            $content = $this->excelTemplateService->generateStudentTemplate();
            $filename = $this->excelTemplateService->getTemplateFilename();
            $encodedFilename = rawurlencode($filename);
            
            return response($content, 200)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . $encodedFilename)
                ->header('Content-Length', strlen($content))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Failed to generate template: :message', ['message' => $e->getMessage()]));
        }
    }

    public function importStudents(Request $request, Course $course, Batch $batch)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $result = $this->excelImportService->importStudents(
                $request->file('file'), 
                $batch,
                $request->user()?->id
            );

            if (isset($result['queued']) && $result['queued'] === true) {
                return redirect()->back()
                    ->with('info', $result['message'] ?? __('admin.import_queued_info'));
            }

            return $this->buildImportResponse($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = $this->extractValidationErrorMessage($e);
            return redirect()->back()
                ->withErrors(['file' => $errorMessage]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => __('Import failed: :message', ['message' => $e->getMessage()])]);
        }
    }

    /**
     * Extract error message from validation exception
     */
    private function extractValidationErrorMessage(\Illuminate\Validation\ValidationException $e): string
    {
        if ($e->errors() && !empty($e->errors())) {
            $firstError = collect($e->errors())->flatten()->first();
            if ($firstError) {
                return $firstError;
            }
        }
        return $e->getMessage();
    }

    /**
     * Build response message for import results
     */
    private function buildImportResponse(array $stats)
    {
        $message = __('Import completed. Created: :created, Added: :added, Skipped: :skipped', [
            'created' => $stats['created'],
            'added' => $stats['added'],
            'skipped' => $stats['skipped'],
        ]);

        if (!empty($stats['errors'])) {
            $message .= ' ' . __('Errors: :count', ['count' => count($stats['errors'])]);
            return redirect()->back()
                ->with('warning', $message)
                ->with('import_errors', $stats['errors']);
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
