<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BatchService;
use Modules\Courses\Requests\StoreLessonRequest;
use Modules\Courses\Requests\UpdateLessonRequest;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\LessonPresentationService;
use Modules\Courses\Services\SectionService;
use Modules\Courses\Services\LessonAttendanceService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use App\Models\Batch;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function __construct(
        private LessonService $lessonService,
        private LessonPresentationService $presentationService,
        private SectionService $sectionService,
        private LessonAttendanceService $attendanceService,
        private BatchService $batchService
    ) {}

    public function index(Course $course)
    {
        $lessons = $this->lessonService->getByCourse($course->id, ['section', 'questions']);
        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Index', [
            'course' => $this->presentationService->formatCourse($course),
            'lessons' => $lessons->map(fn($l) => $this->presentationService->formatLesson($l)),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->presentationService->getLessonTypes(),
        ]);
    }

    public function create(Course $course)
    {
        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Create', [
            'course' => $this->presentationService->formatCourse($course),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->presentationService->getLessonTypes(),
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $data = $request->validated();

        if (isset($data['section_id']) && $data['section_id'] === '') {
            $data['section_id'] = null;
        }

        if (!empty($data['section_id'])) {
            $section = $this->sectionService->getById($data['section_id']);
            if (!$section || !$this->sectionService->belongsToCourse($section, $course)) {
                return redirect()->back()->withErrors(['section_id' => __('Invalid section selected.')]);
            }
        }

        $lessonType = $data['type'] ?? 'text';
        $filePath = $this->lessonService->handleFileUploads($request, $lessonType);
        
        if ($filePath) {
            $data['video_url'] = $filePath;
        } elseif (in_array($lessonType, ['video_file', 'image', 'document_file'])) {
            if (isset($data['video_url']) && filter_var($data['video_url'], FILTER_VALIDATE_URL)) {
                if (!str_contains($data['video_url'], 'storage/') && !str_contains($data['video_url'], 'lessons/')) {
                    unset($data['video_url']);
                }
            }
        }

        $this->lessonService->create($course, $data);

        return redirect()->back()
            ->with('success', __('Lesson created successfully.'));
    }

    public function show(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $lesson->load(['section', 'questions.answers']);
        $batches = Batch::where('course_id', $course->id)
            ->with(['enrollments.student', 'instructor:id,name'])
            ->get();

        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        $students = $this->attendanceService->getStudentsWithAttendance($batches, $lesson, $attendances);
        $attendanceStats = $this->attendanceService->calculateAttendanceStats($attendances, $students);
        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Show', [
            'course' => $this->presentationService->formatCourse($course),
            'lesson' => $this->presentationService->formatLessonFull($lesson),
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->translated_name ?? $b->name,
                'instructor' => $b->instructor ? ['id' => $b->instructor->id, 'name' => $b->instructor->name] : null,
                'students_count' => $b->enrollments->count(),
            ]),
            'students' => $students,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'questionTypes' => $this->presentationService->getQuestionTypes(),
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->presentationService->getLessonTypes(),
        ]);
    }

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

        return redirect()->back()->with('success', __('Attendance marked successfully.'));
    }

    public function attendanceReport(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $batches = Batch::where('course_id', $course->id)
            ->with(['enrollments.student', 'instructor:id,name'])
            ->get();

        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        $students = $this->attendanceService->getStudentsWithAttendance($batches, $lesson, $attendances);

        return Inertia::render('Admin/Lessons/AttendanceReport', [
            'course' => $this->presentationService->formatCourse($course),
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

    public function edit(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $sections = $this->sectionService->getByCourse($course->id);

        return Inertia::render('Admin/Lessons/Edit', [
            'course' => $this->presentationService->formatCourse($course),
            'lesson' => $lesson,
            'sections' => $sections->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->translated_title ?? $s->title,
            ]),
            'lessonTypes' => $this->presentationService->getLessonTypes(),
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
                return redirect()->back()->withErrors(['section_id' => __('Invalid section selected.')]);
            }
        }

        $lessonType = $data['type'] ?? $lesson->type;
        $this->lessonService->handleFileUploadsForUpdate($request, $lesson, $data, $lessonType);

        $this->lessonService->update($lesson, $data);

        return redirect()->back()
            ->with('success', __('Lesson updated successfully.'));
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if (!$this->lessonService->belongsToCourse($lesson, $course)) {
            abort(404);
        }

        $this->lessonService->delete($lesson);

        return redirect()->back()
            ->with('success', __('Lesson deleted successfully.'));
    }

    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        $this->lessonService->reorder($course->id, $validated['lesson_ids']);

        return redirect()->back()->with('success', __('Lessons reordered successfully.'));
    }

    public function liveMeetings()
    {
        try {
            $liveMeetings = $this->lessonService->getAllLiveMeetings();

            return Inertia::render('Admin/LiveMeetings/Index', [
                'liveMeetings' => $liveMeetings->values()->all(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in liveMeetings: ' . $e->getMessage());

            return Inertia::render('Admin/LiveMeetings/Index', [
                'liveMeetings' => [],
            ]);
        }
    }
}
