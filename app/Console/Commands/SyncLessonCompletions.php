<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\LessonAttendance;
use App\Models\LessonCompletion;
use Illuminate\Console\Command;
use Modules\Courses\Services\LessonAttendanceService;

class SyncLessonCompletions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lessons:sync-completions {--course= : Sync for specific course ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync LessonCompletion records from LessonAttendance records';

    /**
     * Execute the console command.
     */
    public function handle(LessonAttendanceService $attendanceService)
    {
        $this->info('Starting to sync lesson completions from attendance records...');

        $query = LessonAttendance::whereIn('status', ['present', 'late', 'excused'])
            ->with(['lesson', 'student', 'batch.course']);

        if ($courseId = $this->option('course')) {
            $query->whereHas('lesson', fn($q) => $q->where('course_id', $courseId));
            $this->info("Filtering for course ID: {$courseId}");
        }

        $attendances = $query->get();
        $total = $attendances->count();
        $synced = 0;
        $skipped = 0;

        $this->info("Found {$total} attendance records to process.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($attendances as $attendance) {
            if (!$attendance->lesson || !$attendance->student || !$attendance->batch || !$attendance->batch->course) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Check if completion already exists
            $exists = LessonCompletion::where('lesson_id', $attendance->lesson_id)
                ->where('student_id', $attendance->student_id)
                ->where('batch_id', $attendance->batch_id)
                ->exists();

            if (!$exists) {
                // Create completion record
                LessonCompletion::create([
                    'lesson_id' => $attendance->lesson_id,
                    'student_id' => $attendance->student_id,
                    'batch_id' => $attendance->batch_id,
                    'completed_at' => $attendance->attended_at ?? now(),
                ]);
                $synced++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Update enrollment progress for all affected enrollments
        $this->info('Updating enrollment progress...');
        $enrollments = Enrollment::whereHas('batch', function($q) use ($courseId) {
            if ($courseId) {
                $q->where('course_id', $courseId);
            }
        })->get();

        $progressBar = $this->output->createProgressBar($enrollments->count());
        $progressBar->start();

        foreach ($enrollments as $enrollment) {
            if ($enrollment->batch && $enrollment->batch->course) {
                $attendanceService->syncCompletionsFromAttendance(
                    $enrollment->student,
                    $enrollment->batch->course,
                    $enrollment->batch_id
                );
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Sync completed!");
        $this->info("Synced: {$synced} new completion records");
        $this->info("Skipped: {$skipped} (already existed)");
        $this->info("Updated enrollment progress for all affected students.");

        return Command::SUCCESS;
    }
}
