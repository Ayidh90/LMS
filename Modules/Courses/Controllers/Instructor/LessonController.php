<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreLessonRequest;
use Modules\Courses\Requests\UpdateLessonRequest;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\LessonAttendanceService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Section;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\UserQuestionAnswer;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function __construct(
        private LessonService $lessonService,
        private LessonAttendanceService $attendanceService,
        private ImageService $imageService
    ) {}

    public function index(Course $course)
    {
        Gate::authorize('viewLessons', $course);

        return redirect()->route('instructor.sections.index', $course);
    }

    public function create(Course $course)
    {
        Gate::authorize('createLesson', $course);

        return Inertia::render('Instructor/Lessons/Create', [
            'course' => $this->formatCourse($course),
            'sections' => $course->sections()->orderBy('order')->get(),
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        Gate::authorize('createLesson', $course);

        $data = $request->validated();
        
        if (isset($data['section_id']) && !$this->sectionBelongsToCourse($data['section_id'], $course)) {
            return back()->withErrors(['section_id' => __('Invalid section selected.')]);
        }

        // Handle file uploads - check request directly, not validated data
        // Clear video_url first if a file is being uploaded
        if ($request->hasFile('video_file') && isset($data['type']) && $data['type'] === 'video_file') {
            $filePath = $this->imageService->upload($request->file('video_file'), 'lessons/videos');
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, remove video_url to prevent storing invalid data
                unset($data['video_url']);
                return back()->withErrors(['video_file' => __('File upload failed. Please try again.')]);
            }
        } elseif ($request->hasFile('image_file') && isset($data['type']) && $data['type'] === 'image') {
            $filePath = $this->imageService->upload($request->file('image_file'), 'lessons/images');
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, remove video_url to prevent storing invalid data
                unset($data['video_url']);
                return back()->withErrors(['image_file' => __('File upload failed. Please try again.')]);
            }
        } elseif ($request->hasFile('document_file') && isset($data['type']) && $data['type'] === 'document_file') {
            $filePath = $this->imageService->upload($request->file('document_file'), 'lessons/documents');
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, remove video_url to prevent storing invalid data
                unset($data['video_url']);
                return back()->withErrors(['document_file' => __('File upload failed. Please try again.')]);
            }
        } elseif (isset($data['type']) && in_array($data['type'], ['video_file', 'image', 'document_file'])) {
            // For file types, if no file is uploaded and video_url is a URL (not a file path), clear it
            // This prevents storing form action URLs or invalid URLs
            if (isset($data['video_url']) && filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
                // If it's a full URL and not a file path, and no file was uploaded, clear it
                if (!str_contains($data['video_url'], 'storage/') && !str_contains($data['video_url'], 'lessons/')) {
                    unset($data['video_url']);
                }
            }
        }

        $this->lessonService->create($course, $data);

        return back()->with('success', __('Lesson created successfully.'));
    }

    public function show(Course $course, Lesson $lesson)
    {
        Gate::authorize('viewLessons', $course);

        $lesson->load(['questions.answers', 'section']);
        $batches = $this->getInstructorBatches($course);
        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        $students = $this->getStudentsWithAttendance($batches, $lesson, $attendances);
        $attendanceStats = $this->calculateAttendanceStats($attendances, $students);
        
        return Inertia::render('Instructor/Lessons/Show', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLesson($lesson),
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id, 
                'name' => $b->translated_name ?? $b->name,
                'students_count' => $b->enrollments->count(),
            ]),
            'students' => $students,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'userAnswers' => $this->getUserAnswersGrouped($lesson),
        ]);
    }

    public function edit(Course $course, Lesson $lesson)
    {
        Gate::authorize('updateLesson', $course);

        return Inertia::render('Instructor/Lessons/Edit', [
            'course' => $this->formatCourse($course),
            'lesson' => $lesson,
            'sections' => $course->sections()->orderBy('order')->get(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        Gate::authorize('updateLesson', $course);

        $data = $request->validated();
        
        if (isset($data['section_id']) && !$this->sectionBelongsToCourse($data['section_id'], $course)) {
            return back()->withErrors(['section_id' => __('Invalid section selected.')]);
        }

        // Handle file uploads - delete old file if new one is uploaded
        $oldVideoUrl = $lesson->video_url;
        
        // Only process file uploads if a new file is actually uploaded
        if ($request->hasFile('video_file') && isset($data['type']) && $data['type'] === 'video_file') {
            $filePath = $this->imageService->upload($request->file('video_file'), 'lessons/videos', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            }
        } elseif ($request->hasFile('image_file') && isset($data['type']) && $data['type'] === 'image') {
            $filePath = $this->imageService->upload($request->file('image_file'), 'lessons/images', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            }
        } elseif ($request->hasFile('document_file') && isset($data['type']) && $data['type'] === 'document_file') {
            $filePath = $this->imageService->upload($request->file('document_file'), 'lessons/documents', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            }
        } elseif (isset($data['type']) && in_array($data['type'], ['video_file', 'image', 'document_file'])) {
            // If no new file is uploaded but type is a file type, preserve existing video_url
            // But first check if video_url is a valid file path, not a URL
            if (isset($data['video_url']) && $data['video_url'] !== '') {
                // If video_url is a full URL (not a file path), clear it unless it's a valid file URL
                if (filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
                    // If it's a full URL and not a file path, and no file was uploaded, preserve old one or clear
                    if (!str_contains($data['video_url'], 'storage/') && !str_contains($data['video_url'], 'lessons/') && !str_contains($data['video_url'], 'videos.serve')) {
                        // Invalid URL for file type - use old one or clear
                        if ($oldVideoUrl) {
                            $data['video_url'] = $oldVideoUrl;
                        } else {
                            unset($data['video_url']);
                        }
                    }
                }
                // If it's not a URL (it's a file path), keep it as is
            } else {
                // If video_url is not in data or is empty, preserve the old one
                if ($oldVideoUrl) {
                    $data['video_url'] = $oldVideoUrl;
                }
            }
        }

        $this->lessonService->update($lesson, $data);

        return redirect()->route('instructor.sections.index', $course)
            ->with('success', __('Lesson updated successfully.'));
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        Gate::authorize('deleteLesson', $course);

        $this->lessonService->delete($lesson);

        return redirect()->route('instructor.sections.index', $course)
            ->with('success', __('Lesson deleted successfully.'));
    }

    public function markAttendance(Request $request, Course $course, Lesson $lesson)
    {
        Gate::authorize('markAttendance', $course);

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.batch_id' => 'required|exists:batches,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string|max:1000',
            'attendances.*.attended_at' => 'nullable|date',
        ]);

        $instructor = Auth::user();
        $validAttendances = $this->filterValidAttendances($validated['attendances'], $instructor);

        // instructorMarkAttendance already handles progress updates internally
        $this->attendanceService->instructorMarkAttendance($lesson, $instructor, $validAttendances);

        return back()->with('success', __('Attendance marked successfully.'));
    }

    private function sectionBelongsToCourse(int $sectionId, Course $course): bool
    {
        return Section::where('id', $sectionId)->where('course_id', $course->id)->exists();
    }

    private function getInstructorBatches(Course $course)
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', Auth::id())
            ->with(['enrollments.student'])
            ->get();
    }

    private function getStudentsFromBatches($batches)
    {
        $students = collect();
        
        foreach ($batches as $batch) {
            foreach ($batch->enrollments as $enrollment) {
                $students->push([
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->translated_name ?? $batch->name,
                    'enrollment_progress' => $enrollment->progress ?? 0,
                ]);
            }
        }
        
        return $students->values();
    }

    private function getStudentsWithAttendance($batches, Lesson $lesson, $attendances)
    {
        $students = collect();
        $attendanceMap = $attendances->keyBy(fn($a) => $a['student_id'] . '-' . $a['batch_id']);
        
        foreach ($batches as $batch) {
            foreach ($batch->enrollments as $enrollment) {
                $key = $enrollment->student_id . '-' . $batch->id;
                $attendance = $attendanceMap->get($key);
                
                // Get completion status
                $completion = LessonCompletion::where('lesson_id', $lesson->id)
                    ->where('student_id', $enrollment->student_id)
                    ->where('batch_id', $batch->id)
                    ->first();
                
                $students->push([
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->translated_name ?? $batch->name,
                    'enrollment_progress' => $enrollment->progress ?? 0,
                    'attendance' => $attendance ? [
                        'status' => $attendance['status'],
                        'notes' => $attendance['notes'],
                        'attended_at' => $attendance['attended_at'],
                        'marked_by' => $attendance['marked_by_type'] ?? ($attendance['student_id'] === ($attendance['marked_by'] ?? null) ? 'student' : 'instructor'),
                    ] : null,
                    'completed' => $completion ? true : false,
                    'completed_at' => $completion?->completed_at,
                ]);
            }
        }
        
        return $students;
    }

    private function calculateAttendanceStats($attendances, $students)
    {
        $total = $students->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $excused = $attendances->where('status', 'excused')->count();
        $notMarked = $total - $attendances->count();
        
        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'not_marked' => $notMarked,
            'attendance_rate' => $total > 0 ? round((($present + $late + $excused) / $total) * 100) : 0,
        ];
    }

    private function getUserAnswersGrouped(Lesson $lesson)
    {
        return UserQuestionAnswer::whereIn('question_id', $lesson->questions->pluck('id'))
            ->with(['user:id,name,email', 'answer:id,answer,answer_ar'])
            ->get()
            ->groupBy('question_id')
            ->map(fn($answers) => $answers->map(fn($a) => [
                'id' => $a->id,
                'user' => ['id' => $a->user->id, 'name' => $a->user->name],
                'answer_id' => $a->answer_id,
                'answer_text' => $a->answer_text,
                'is_correct' => $a->is_correct,
                'points_earned' => $a->points_earned,
            ]));
    }

    private function filterValidAttendances(array $attendances, $instructor): array
    {
        return array_filter($attendances, function ($data) use ($instructor) {
            $batch = Batch::find($data['batch_id']);
            if (!$batch || ($batch->instructor_id !== $instructor->id && !$instructor->isAdmin())) {
                return false;
            }
            return Enrollment::where('batch_id', $data['batch_id'])
                ->where('student_id', $data['student_id'])
                ->exists();
        });
    }

    private function updateProgress(Course $course, Enrollment $enrollment): void
    {
        $totalLessons = $course->lessons()->count();
        if ($totalLessons === 0) return;

        // Sync LessonCompletion records from LessonAttendance first
        // This ensures all attendance records (from instructor or student) are reflected in progress
        $this->attendanceService->syncCompletionsFromAttendance($enrollment->student, $course, $enrollment->batch_id);

        $completedLessons = LessonCompletion::where('student_id', $enrollment->student_id)
            ->where('batch_id', $enrollment->batch_id)
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();

        $progress = min((int) round(($completedLessons / $totalLessons) * 100), 100);
        $enrollment->update(['progress' => $progress]);

        if ($completedLessons >= $totalLessons && $enrollment->status !== 'completed') {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
        }
    }

    private function formatCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->translated_title,
            'slug' => $course->slug,
        ];
    }

    private function formatLesson(Lesson $lesson): array
    {
        return $this->lessonService->formatForFrontend($lesson);
    }
}
