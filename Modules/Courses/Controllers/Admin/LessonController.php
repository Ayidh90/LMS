<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Courses\Requests\StoreLessonRequest;
use Modules\Courses\Requests\UpdateLessonRequest;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\SectionService;
use Modules\Courses\Services\LessonAttendanceService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\LessonAttendance;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function __construct(
        private LessonService $lessonService,
        private SectionService $sectionService,
        private LessonAttendanceService $attendanceService
    ) {}

    public function index(Course $course)
    {
        $lessons = $this->lessonService->getByCourse($course->id, ['section', 'questions']);
        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Index', [
            'course' => $this->formatCourse($course),
            'lessons' => $lessons->map(fn($l) => $this->formatLesson($l)),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->getLessonTypes(),
        ]);
    }

    public function create(Course $course)
    {
        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Create', [
            'course' => $this->formatCourse($course),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->getLessonTypes(),
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $data = $request->validated();

        // Convert empty string to null for section_id
        if (isset($data['section_id']) && $data['section_id'] === '') {
            $data['section_id'] = null;
        }

        if (!empty($data['section_id'])) {
            $section = $this->sectionService->getById($data['section_id']);
            if (!$section || !$this->sectionService->belongsToCourse($section, $course)) {
                return back()->withErrors(['section_id' => __('Invalid section selected.')]);
            }
        }

        $this->lessonService->create($course, $data);

        // If request wants to stay on same page (from modal), return back to course show
        if ($request->header('X-Inertia')) {
            return redirect()->route('admin.courses.show', $course)
                ->with('success', __('Lesson created successfully.'));
        }

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', __('Lesson created successfully.'));
    }

    public function show(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $lesson->load(['section', 'questions.answers']);

        // Get all batches for this course
        $batches = Batch::where('course_id', $course->id)
            ->with(['enrollments.student', 'instructor:id,name'])
            ->get();

        // Get attendance data
        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));

        // Get students with their attendance status
        $students = $this->getStudentsWithAttendance($batches, $lesson, $attendances);

        // Calculate attendance stats
        $attendanceStats = $this->calculateAttendanceStats($attendances, $students);

        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Show', [
            'course' => $this->formatCourse($course),
            'lesson' => $this->formatLessonFull($lesson),
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->translated_name ?? $b->name,
                'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
                'students_count' => $b->enrollments->count(),
            ]),
            'students' => $students,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'questionTypes' => $this->getQuestionTypes(),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->getLessonTypes(),
        ]);
    }

    /**
     * Mark attendance for students (Admin can mark for any batch)
     */
    public function markAttendance(Request $request, Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.batch_id' => 'required|exists:batches,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string|max:1000',
        ]);

        $admin = Auth::user();

        // Validate all attendances belong to this course
        $validAttendances = [];
        foreach ($validated['attendances'] as $data) {
            $batch = Batch::find($data['batch_id']);
            if (!$batch || $batch->course_id !== $course->id) {
                continue;
            }

            $enrollment = Enrollment::where('batch_id', $data['batch_id'])
                ->where('student_id', $data['student_id'])
                ->first();

            if ($enrollment) {
                $validAttendances[] = $data;
            }
        }

        $this->attendanceService->instructorMarkAttendance($lesson, $admin, $validAttendances);

        return back()->with('success', __('Attendance marked successfully.'));
    }

    /**
     * Get attendance report for a lesson
     */
    public function attendanceReport(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $batches = Batch::where('course_id', $course->id)
            ->with(['enrollments.student', 'instructor:id,name'])
            ->get();

        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        $students = $this->getStudentsWithAttendance($batches, $lesson, $attendances);

        return Inertia::render('Admin/Lessons/AttendanceReport', [
            'course' => $this->formatCourse($course),
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->translated_title ?? $lesson->title,
                'type' => $lesson->type,
            ],
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->translated_name ?? $b->name,
                'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
            ]),
            'students' => $students,
            'attendances' => $attendances,
        ]);
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
                        'marked_by' => $attendance['student_id'] === ($attendance['marked_by'] ?? null) ? 'student' : 'instructor',
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

    public function edit(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Edit', [
            'course' => $this->formatCourse($course),
            'lesson' => $lesson,
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->getLessonTypes(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $data = $request->validated();

        if (isset($data['section_id'])) {
            $section = $this->sectionService->getById($data['section_id']);
            if (!$section || !$this->sectionService->belongsToCourse($section, $course)) {
                return back()->withErrors(['section_id' => __('Invalid section selected.')]);
            }
        }

        $this->lessonService->update($lesson, $data);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', __('Lesson updated successfully.'));
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $this->lessonService->delete($lesson);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', __('Lesson deleted successfully.'));
    }

    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        $this->lessonService->reorder($course->id, $validated['lesson_ids']);

        return back()->with('success', __('Lessons reordered successfully.'));
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
        return [
            'id' => $lesson->id,
            'title' => $lesson->translated_title ?? $lesson->title,
            'title_ar' => $lesson->title_ar,
            'type' => $lesson->type,
            'order' => $lesson->order,
            'duration_minutes' => $lesson->duration_minutes,
            'is_free' => $lesson->is_free,
            'section' => $lesson->section ? [
                'id' => $lesson->section->id,
                'title' => $lesson->section->translated_title ?? $lesson->section->title,
            ] : null,
            'questions_count' => $lesson->questions ? $lesson->questions->count() : 0,
        ];
    }

    private function formatLessonFull(Lesson $lesson): array
    {
        return [
            ...$this->formatLesson($lesson),
            'description' => $lesson->translated_description ?? $lesson->description,
            'description_ar' => $lesson->description_ar,
            'content' => $lesson->translated_content ?? $lesson->content,
            'content_ar' => $lesson->content_ar,
            'video_url' => $lesson->video_url,
            'live_meeting_date' => $lesson->live_meeting_date,
            'live_meeting_link' => $lesson->live_meeting_link,
            'questions' => $lesson->questions ? $lesson->questions->map(fn($q) => [
                'id' => $q->id,
                'type' => $q->type,
                'question' => $q->translated_question ?? $q->question,
                'points' => $q->points,
                'order' => $q->order,
                'answers_count' => $q->answers ? $q->answers->count() : 0,
            ]) : [],
        ];
    }

    private function getLessonTypes(): array
    {
        return [
            ['value' => 'text', 'label' => __('lessons.types.text')],
            ['value' => 'youtube_video', 'label' => __('lessons.types.youtube_video')],
            ['value' => 'google_drive_video', 'label' => __('lessons.types.google_drive_video')],
            ['value' => 'video_file', 'label' => __('lessons.types.video_file')],
            ['value' => 'image', 'label' => __('lessons.types.image')],
            ['value' => 'document_file', 'label' => __('lessons.types.document_file')],
            ['value' => 'embed_frame', 'label' => __('lessons.types.embed_frame')],
            ['value' => 'assignment', 'label' => __('lessons.types.assignment')],
            ['value' => 'test', 'label' => __('lessons.types.test')],
            ['value' => 'live', 'label' => __('lessons.types.live')],
        ];
    }

    private function getQuestionTypes(): array
    {
        return [
            ['value' => 'multiple_choice', 'label' => __('Multiple Choice')],
            ['value' => 'true_false', 'label' => __('True/False')],
            ['value' => 'short_answer', 'label' => __('Short Answer')],
            ['value' => 'essay', 'label' => __('Essay')],
        ];
    }

    /**
     * Display all live meetings across all courses
     */
    public function liveMeetings()
    {
        try {
            // Get all live lessons with their relationships
            $liveLessons = Lesson::where('type', 'live')
                ->with(['course', 'section'])
                ->orderByRaw('CASE WHEN live_meeting_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('live_meeting_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            $mappedMeetings = $liveLessons->map(function ($lesson) {
                try {
                    // Format date as stored in database (YYYY-MM-DD HH:mm:ss) without timezone conversion
                    // This ensures the date matches exactly what's in the database
                    $dateString = null;
                    if ($lesson->live_meeting_date) {
                        $dateString = $lesson->live_meeting_date->format('Y-m-d H:i:s');
                    }

                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->translated_title ?? $lesson->title ?? 'Untitled Lesson',
                        'description' => $lesson->translated_description ?? $lesson->description ?? null,
                        'live_meeting_date' => $dateString,
                        'live_meeting_link' => $lesson->live_meeting_link,
                        'order' => $lesson->order ?? 0,
                        'is_free' => $lesson->is_free ?? false,
                        'course' => $lesson->course ? [
                            'id' => $lesson->course->id,
                            'title' => $lesson->course->translated_title ?? $lesson->course->title ?? 'Unknown Course',
                            'slug' => $lesson->course->slug ?? null,
                        ] : null,
                        'section' => $lesson->section ? [
                            'id' => $lesson->section->id,
                            'title' => $lesson->section->translated_title ?? $lesson->section->title ?? 'Unknown Section',
                            'order' => $lesson->section->order ?? 0,
                        ] : null,
                        'duration_minutes' => $lesson->duration_minutes ?? 60,
                    ];
                } catch (\Exception $e) {
                    Log::error('Error mapping lesson: ' . $e->getMessage(), [
                        'lesson_id' => $lesson->id ?? null
                    ]);
                    return null;
                }
            })->filter(); // Remove null values

            return Inertia::render('Admin/LiveMeetings/Index', [
                'liveMeetings' => $mappedMeetings->values()->all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in liveMeetings: ' . $e->getMessage());

            return Inertia::render('Admin/LiveMeetings/Index', [
                'liveMeetings' => [],
            ]);
        }
    }

}
