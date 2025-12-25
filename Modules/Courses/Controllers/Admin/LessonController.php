<?php

namespace Modules\Courses\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
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
        private LessonAttendanceService $attendanceService,
        private ImageService $imageService
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

        // Handle video_url and file uploads based on lesson type
        $oldVideoUrl = $lesson->video_url;
        $lessonType = $data['type'] ?? $lesson->type;
        
        // Handle file uploads for file-based lesson types
        if ($request->hasFile('video_file') && $lessonType === 'video_file') {
            $filePath = $this->imageService->upload($request->file('video_file'), 'lessons/videos', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, preserve old video_url
                if ($oldVideoUrl) {
                    $data['video_url'] = $oldVideoUrl;
                } else {
                    unset($data['video_url']);
                }
                return back()->withErrors(['video_file' => __('File upload failed. Please try again.')]);
            }
        } elseif ($request->hasFile('image_file') && $lessonType === 'image') {
            $filePath = $this->imageService->upload($request->file('image_file'), 'lessons/images', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, preserve old video_url
                if ($oldVideoUrl) {
                    $data['video_url'] = $oldVideoUrl;
                } else {
                    unset($data['video_url']);
                }
                return back()->withErrors(['image_file' => __('File upload failed. Please try again.')]);
            }
        } elseif ($request->hasFile('document_file') && $lessonType === 'document_file') {
            $filePath = $this->imageService->upload($request->file('document_file'), 'lessons/documents', $oldVideoUrl);
            if ($filePath) {
                $data['video_url'] = $filePath;
            } else {
                // If upload failed, preserve old video_url
                if ($oldVideoUrl) {
                    $data['video_url'] = $oldVideoUrl;
                } else {
                    unset($data['video_url']);
                }
                return back()->withErrors(['document_file' => __('File upload failed. Please try again.')]);
            }
        } else {
            // No new file uploaded - handle video_url based on lesson type
            if (in_array($lessonType, ['video_file', 'image', 'document_file'])) {
                // For file types, preserve existing video_url if no new file is uploaded
                if (!isset($data['video_url']) || $data['video_url'] === null || $data['video_url'] === '') {
                    // If video_url is not provided or is empty, preserve the old one
                        if ($oldVideoUrl) {
                            $data['video_url'] = $oldVideoUrl;
                    } else {
                        $data['video_url'] = null;
                    }
                } else {
                    // If video_url is provided, check if it's a formatted URL or a file path
                    // If it's a formatted URL (contains route path), extract the original file path
                    // Otherwise, use it as is (it's already a file path)
                    $videoUrl = $data['video_url'];
                    if (filter_var($videoUrl, FILTER_VALIDATE_URL) && str_contains($videoUrl, 'videos.serve')) {
                        // It's a formatted URL - extract the path parameter
                        // URL format: /videos/serve?path=lessons/videos/file.mp4
                        $parsedUrl = parse_url($videoUrl);
                        if (isset($parsedUrl['query'])) {
                            parse_str($parsedUrl['query'], $queryParams);
                            if (isset($queryParams['path'])) {
                                $data['video_url'] = $queryParams['path'];
                            } else {
                                // Couldn't extract path, preserve old one
                                $data['video_url'] = $oldVideoUrl ?: null;
                            }
                        } else {
                            // Couldn't parse URL, preserve old one
                            $data['video_url'] = $oldVideoUrl ?: null;
                    }
                }
                    // If it's not a formatted URL, it's already a file path - use it as is
                }
            } elseif (in_array($lessonType, ['youtube_video', 'google_drive_video', 'embed_frame'])) {
                // For URL-based types, use the provided video_url (already validated in request)
                // If video_url is not provided or is empty, set it to null
                if (!isset($data['video_url']) || $data['video_url'] === null || $data['video_url'] === '') {
                    $data['video_url'] = null;
                }
            } else {
                // For other types (text, assignment, test, live), clear video_url
                $data['video_url'] = null;
            }
        }
        
        // Handle live_meeting_date and live_meeting_link for live type
        if ($lessonType === 'live') {
            // Ensure live_meeting_date and live_meeting_link are set (or null if not provided)
            if (!isset($data['live_meeting_date']) || $data['live_meeting_date'] === '') {
                $data['live_meeting_date'] = null;
            }
            if (!isset($data['live_meeting_link']) || $data['live_meeting_link'] === '') {
                $data['live_meeting_link'] = null;
            }
        } else {
            // For non-live types, clear live_meeting fields
            $data['live_meeting_date'] = null;
            $data['live_meeting_link'] = null;
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
        // Use formatForFrontend to ensure video_url is properly formatted
        $formatted = $this->lessonService->formatForFrontend($lesson);
        
        // Add section info for list views
        return [
            ...$formatted,
            'section' => $lesson->section ? [
                'id' => $lesson->section->id,
                'title' => $lesson->section->translated_title ?? $lesson->section->title,
            ] : null,
            'questions_count' => $lesson->questions ? $lesson->questions->count() : 0,
        ];
    }

    private function formatLessonFull(Lesson $lesson): array
    {
        // Use formatForFrontend to ensure video_url is properly formatted with full path
        $formatted = $this->lessonService->formatForFrontend($lesson);
        
        // Add section info and full question details
        // Also include original title field for editing (formatForFrontend returns translated title in 'title' field)
        return [
            ...$formatted,
            'translated_title' => $formatted['title'], // The translated title from formatForFrontend
            'title' => $lesson->title, // Original English title for editing
            'translated_description' => $formatted['description'], // The translated description
            'description' => $lesson->description, // Original English description for editing
            'translated_content' => $formatted['content'], // The translated content
            'content' => $lesson->content, // Original English content for editing
            'translated_video_url' => $formatted['video_url'], // The formatted video_url from formatForFrontend
            'video_url' => $lesson->video_url, // Original video_url (file path or URL) for editing
            'section' => $lesson->section ? [
                'id' => $lesson->section->id,
                'title' => $lesson->section->translated_title ?? $lesson->section->title,
            ] : null,
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
