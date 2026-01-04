<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\User;
use App\Models\Enrollment;
use App\Jobs\ImportStudentsJob;
use App\Repositories\BatchRepository;
use Modules\Roles\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExcelImportService
{
    public function __construct(
        private UserService $userService,
        private BatchRepository $batchRepository
    ) {}

    /**
     * Check if PhpSpreadsheet is available
     */
    public function isAvailable(): bool
    {
        return class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class);
    }

    /**
     * Import students from Excel file
     * If more than 20 students, dispatches a job for async processing
     * 
     * @return array|string Returns stats array for sync processing, or job ID string for async
     */
    public function importStudents(UploadedFile $file, Batch $batch, ?int $userId = null): array|string
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('Excel library is not installed. Please install phpoffice/phpspreadsheet package.');
        }

        // Load data to count rows
        $rows = $this->loadSpreadsheetData($file);
        
        if (empty($rows)) {
            throw new \RuntimeException('Excel file is empty or has no data rows.');
        }

        $studentCount = count($rows);

        // Check max_students limit before processing
        if ($batch->max_students !== null) {
            $currentStudents = \App\Models\Enrollment::where('batch_id', $batch->id)->count();
            $wouldBeTotal = $currentStudents + $studentCount;
            
            if ($wouldBeTotal > $batch->max_students) {
                $validator = validator([], []);
                $validator->errors()->add('file', __('admin.max_students_would_exceed', [
                    'count' => $studentCount,
                    'max' => $batch->max_students,
                    'current' => $currentStudents,
                    'would_be' => $wouldBeTotal,
                ]));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        // If more than 20 students, dispatch job for async processing
        if ($studentCount > 20) {
            // Store the file temporarily
            $filePath = 'imports/students_' . $batch->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($filePath, file_get_contents($file->getPathname()));

            // Dispatch the job
            $job = ImportStudentsJob::dispatch($batch->id, $filePath, $userId);

            return [
                'queued' => true,
                'job_id' => $job->getJobId(),
                'message' => __('admin.import_queued', ['count' => $studentCount]),
                'student_count' => $studentCount,
            ];
        }

        // Process synchronously for 20 or fewer students
        return $this->processImport($rows, $batch);
    }

    /**
     * Process import directly from file (used by job)
     */
    public function processImportDirectly(UploadedFile $file, Batch $batch): array
    {
        $rows = $this->loadSpreadsheetData($file);
        
        if (empty($rows)) {
            throw new \RuntimeException('Excel file is empty or has no data rows.');
        }

        // Check max_students limit before processing
        if ($batch->max_students !== null) {
            $currentStudents = \App\Models\Enrollment::where('batch_id', $batch->id)->count();
            $newStudentsCount = count($rows);
            $wouldBeTotal = $currentStudents + $newStudentsCount;
            
            if ($wouldBeTotal > $batch->max_students) {
                $validator = validator([], []);
                $validator->errors()->add('file', __('admin.max_students_would_exceed', [
                    'count' => $newStudentsCount,
                    'max' => $batch->max_students,
                    'current' => $currentStudents,
                    'would_be' => $wouldBeTotal,
                ]));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        return $this->processImport($rows, $batch);
    }

    /**
     * Process the import synchronously
     */
    private function processImport(array $rows, Batch $batch): array
    {
        $studentRole = $this->getStudentRole();
        $stats = [
            'created' => 0,
            'added' => 0,
            'skipped' => 0,
            'errors' => [],
            'queued' => false,
        ];

        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $result = $this->processStudentRow($row, $rowNumber, $batch, $studentRole);
                
                $stats['created'] += $result['created'] ? 1 : 0;
                $stats['added'] += $result['added'] ? 1 : 0;
                $stats['skipped'] += $result['skipped'] ? 1 : 0;
                
                if (!empty($result['error'])) {
                    $stats['errors'][] = $result['error'];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $stats;
    }

    /**
     * Load and parse spreadsheet data
     */
    private function loadSpreadsheetData(UploadedFile $file): array
    {
        $extension = $file->getClientOriginalExtension();
        
        // Load the spreadsheet based on file type
        // @phpstan-ignore-next-line
        $reader = match($extension) {
            'csv' => new \PhpOffice\PhpSpreadsheet\Reader\Csv(),
            'xls' => new \PhpOffice\PhpSpreadsheet\Reader\Xls(),
            default => new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(),
        };

        // @phpstan-ignore-next-line
        $spreadsheet = $reader->load($file->getPathname());
        // @phpstan-ignore-next-line
        $sheet = $spreadsheet->getActiveSheet();
        // @phpstan-ignore-next-line
        $rows = $sheet->toArray();

        // Remove header row and return data rows
        if (count($rows) < 2) {
            return [];
        }

        array_shift($rows);
        return $rows;
    }

    /**
     * Get student role from database
     */
    private function getStudentRole(): ?Role
    {
        return Role::where('slug', 'student')
            ->orWhere('name', 'student')
            ->first();
    }

    /**
     * Process a single student row from Excel
     */
    private function processStudentRow(array $row, int $rowNumber, Batch $batch, ?Role $studentRole): array
    {
        $result = [
            'created' => false,
            'added' => false,
            'skipped' => false,
            'error' => null,
        ];

        // Extract and validate row data
        $name = trim($row[0] ?? '');
        $email = trim($row[1] ?? '');
        $nationalId = isset($row[2]) ? trim($row[2]) : null;

        // Skip empty rows
        if (empty($name) && empty($email)) {
            $result['skipped'] = true;
            return $result;
        }

        // Validate required fields
        if (empty($name) || empty($email)) {
            $result['error'] = __('Row :row: Name and Email are required.', ['row' => $rowNumber]);
            $result['skipped'] = true;
            return $result;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['error'] = __('Row :row: Invalid email format: :email', ['row' => $rowNumber, 'email' => $email]);
            $result['skipped'] = true;
            return $result;
        }

        // Find or create user
        $user = $this->findOrCreateUser($name, $email, $nationalId, $studentRole);
        
        if ($user->wasRecentlyCreated) {
            $result['created'] = true;
        }

        // Ensure user has student role
        $this->userService->ensureStudentRole($user, $studentRole);

        // Add user to batch if not already enrolled
        if ($this->enrollUserInBatch($user, $batch)) {
            $result['added'] = true;
        } else {
            $result['skipped'] = true;
        }

        return $result;
    }

    /**
     * Find existing user or create new one
     */
    private function findOrCreateUser(string $name, string $email, ?string $nationalId, ?Role $studentRole): User
    {
        // Try to find user by email or national_id
        $user = $this->userService->findUserByEmailOrNationalId($email, $nationalId);

        if ($user) {
            // Update existing user if needed
            $this->userService->updateUserInfo($user, $name, $nationalId);
            return $user;
        }

        // Create new student user
        return $this->userService->createStudentUser([
            'name' => $name,
            'email' => $email,
            'national_id' => $nationalId,
        ], $studentRole);
    }

    /**
     * Enroll user in batch if not already enrolled
     */
    private function enrollUserInBatch(User $user, Batch $batch): bool
    {
        if ($this->batchRepository->isUserEnrolled($batch, $user->id)) {
            return false;
        }

        // Check max_students limit if set (validation is done in repository)
        $this->batchRepository->enrollUser($batch, $user->id);
        return true;
    }
}

