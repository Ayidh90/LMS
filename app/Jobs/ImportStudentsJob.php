<?php

namespace App\Jobs;

use App\Models\Batch;
use App\Services\ExcelImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string
     */
    public $queue = 'default';

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $batchId,
        public string $filePath,
        public ?int $userId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ExcelImportService $excelImportService): void
    {
        try {
            // Get the batch
            $batch = Batch::findOrFail($this->batchId);

            // Check if file still exists
            if (!Storage::disk('local')->exists($this->filePath)) {
                Log::error("ImportStudentsJob: File not found at path: {$this->filePath}");
                throw new \Exception("File not found for import");
            }

            // Get the full path to the stored file
            $fullPath = Storage::disk('local')->path($this->filePath);
            
            // Create a temporary file in the system temp directory
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_');
            copy($fullPath, $tempFile);
            
            // Create UploadedFile instance
            $originalName = basename($this->filePath);
            $mimeType = mime_content_type($fullPath) ?: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            
            $file = new \Illuminate\Http\UploadedFile(
                $tempFile,
                $originalName,
                $mimeType,
                null,
                true // test mode
            );

            // Import students (this will process synchronously since we're already in a job)
            // We use processImportDirectly to bypass the queue check
            $stats = $excelImportService->processImportDirectly($file, $batch);
            
            // Clean up temp file
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }

            // Log the results
            Log::info("ImportStudentsJob completed", [
                'batch_id' => $this->batchId,
                'created' => $stats['created'],
                'added' => $stats['added'],
                'skipped' => $stats['skipped'],
                'errors_count' => count($stats['errors']),
            ]);

            // Clean up the temporary file
            Storage::disk('local')->delete($this->filePath);

        } catch (\Exception $e) {
            Log::error("ImportStudentsJob failed", [
                'batch_id' => $this->batchId,
                'file_path' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up the temporary file even on failure
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ImportStudentsJob failed permanently", [
            'batch_id' => $this->batchId,
            'file_path' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);

        // Clean up the temporary file
        if (Storage::disk('local')->exists($this->filePath)) {
            Storage::disk('local')->delete($this->filePath);
        }
    }
}

