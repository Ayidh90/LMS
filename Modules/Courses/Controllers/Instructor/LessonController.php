<?php

namespace Modules\Courses\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\BatchService;
use App\Traits\HasEffectiveRole;
use Modules\Courses\Requests\StoreLessonRequest;
use Modules\Courses\Requests\UpdateLessonRequest;
use Modules\Courses\Requests\MarkAttendanceRequest;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\LessonPresentationService;
use Modules\Courses\Services\SectionService;
use Modules\Courses\Services\LessonAttendanceService;
use Modules\Courses\Services\QuestionService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LessonController extends Controller
{
    use HasEffectiveRole;
    
    public function __construct(
        private LessonService $lessonService,
        private LessonPresentationService $presentationService,
        private SectionService $sectionService,
        private LessonAttendanceService $attendanceService,
        private BatchService $batchService,
        private QuestionService $questionService
    ) {}

    public function index(Course $course)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('viewLessons', $course);

        return redirect()->route('instructor.sections.index', $course);
    }

    public function create(Course $course)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('createLesson', $course);

        return Inertia::render('Instructor/Lessons/Create', [
            'course' => $this->presentationService->formatCourse($course),
            'sections' => $course->sections()->orderBy('order')->get(),
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('createLesson', $course);

        $data = $request->validated();
        
        if (isset($data['section_id'])) {
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
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('viewLessons', $course);

        $lesson->load(['questions.answers', 'section']);
        $batches = $this->batchService->getInstructorBatchesForCourse($course, Auth::id());
        $attendances = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        $students = $this->attendanceService->getStudentsWithAttendance($batches, $lesson, $attendances);
        $attendanceStats = $this->attendanceService->calculateAttendanceStats($attendances, $students);
        
        return Inertia::render('Instructor/Lessons/Show', [
            'course' => $this->presentationService->formatCourse($course),
            'lesson' => $this->lessonService->formatForFrontend($lesson, null, true),
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id, 
                'name' => $b->translated_name ?? $b->name,
                'students_count' => $b->enrollments->count(),
            ]),
            'students' => $students,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'userAnswers' => $this->questionService->getUserAnswersGrouped($lesson),
        ]);
    }

    public function edit(Course $course, Lesson $lesson)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('updateLesson', $course);

        return Inertia::render('Instructor/Lessons/Edit', [
            'course' => $this->presentationService->formatCourse($course),
            'lesson' => $lesson,
            'sections' => $course->sections()->orderBy('order')->get(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('updateLesson', $course);

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
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('deleteLesson', $course);

        $this->lessonService->delete($lesson);

        return redirect()->back()
            ->with('success', __('Lesson deleted successfully.'));
    }

    public function markAttendance(MarkAttendanceRequest $request, Course $course, Lesson $lesson)
    {
        $this->ensureEffectiveRole('instructor');
        Gate::authorize('markAttendance', $course);

        $instructor = Auth::user();
        $validAttendances = $this->attendanceService->filterValidAttendances(
            $request->validated()['attendances'],
            $instructor
        );

        $this->attendanceService->instructorMarkAttendance($lesson, $instructor, $validAttendances);

        return redirect()->back()
            ->with('success', __('Attendance marked successfully.'));
    }
}
