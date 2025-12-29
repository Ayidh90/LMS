<?php

namespace Modules\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HasEffectiveRole;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Services\FavoriteService;
use Modules\Courses\Services\LessonService;
use Modules\Courses\Services\LessonAttendanceService;
use App\Models\User;
use App\Models\LessonCompletion;
use App\Models\Enrollment;
use App\Models\Batch;
use App\Models\UserQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoursePlayerController extends Controller
{
    use HasEffectiveRole;
    
    public function __construct(
        private CourseService $courseService,
        private FavoriteService $favoriteService,
        private LessonService $lessonService,
        private LessonAttendanceService $attendanceService
    ) {}

    public function show(Course $course, ?Lesson $lesson = null)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if ($user) {
            $user->refresh();
            $effectiveRole = $user->getEffectiveRole();
            
            if ($effectiveRole === 'instructor' && $user->isInstructor()) {
                $hasAccess = Batch::where('course_id', $course->id)
                    ->where('instructor_id', $user->id)
                    ->exists();
                
                if ($hasAccess && $lesson) {
                    return redirect()->route('instructor.lessons.show', [
                        $course->slug ?? $course->id,
                        $lesson->id
                    ]);
                }
            }
            
            $availableRoles = $user->getAvailableRolesForSelection();
            if (count($availableRoles) > 1 && $effectiveRole !== 'student' && !$user->isAdmin()) {
                abort(403, __('You must switch to student role to access this page.'));
            }
        }
        
        $this->validateAccess($user, $course);

        $courseData = $this->courseService->getCourseById($course->id);
        if (!$courseData) {
            abort(404, __('messages.courses.not_found'));
        }

        $lessons = $this->lessonService->getByCourse($course->id, ['questions.answers']);
        $sections = $course->sections()->with(['lessons' => fn($q) => $q->with('questions.answers')->orderBy('order')])->orderBy('order')->get();
        $currentLesson = $this->getCurrentLesson($lesson, $lessons);

        if ($user) {
            $user->refresh();
        }
        $effectiveRole = $user ? $user->getEffectiveRole() : null;
        
        $studentAttendance = null;
        $studentProgress = null;
        $enrollment = null;
        if ($user instanceof User && $effectiveRole === 'student' && $user->isStudent()) {
            $enrollment = Enrollment::where('student_id', $user->id)
                ->whereHas('batch', fn($q) => $q->where('course_id', $course->id))
                ->first();
        }
        
        if ($user instanceof User && $effectiveRole === 'student' && $user->isStudent() && $enrollment) {
            $studentProgress = $this->attendanceService->getStudentCourseProgress($user, $course, $enrollment->batch_id);
            
            if ($currentLesson) {
                $this->attendanceService->autoMarkAttendance($user, $course, $currentLesson);
                $studentProgress = $this->attendanceService->getStudentCourseProgress($user, $course, $enrollment->batch_id);
                $studentAttendance = $this->attendanceService->getStudentLessonAttendance($user, $currentLesson, $enrollment->batch_id);
            }
        }

        $userAnswers = $this->getUserAnswers($user, $currentLesson);
        $instructorData = $this->getInstructorData($user, $course, $currentLesson);

        return Inertia::render('Courses/Player', [
            'course' => $this->formatCourse($courseData),
            'lesson' => $currentLesson ? $this->lessonService->formatForFrontend($currentLesson, $userAnswers, $instructorData['isInstructor']) : null,
            'lessons' => $this->formatLessonsList($lessons, $user, $course),
            'sections' => $this->formatSectionsList($sections, $user, $course),
            'isFavorited' => $user ? $this->favoriteService->isFavorited($courseData) : false,
            'isEnrolled' => $user instanceof User ? $user->canAccessCourse($course) : false,
            'isInstructor' => $instructorData['isInstructor'],
            'instructorBatches' => $instructorData['batches'],
            'instructorStudents' => $instructorData['students'],
            'instructorAttendances' => $instructorData['attendances'],
            'studentAttendance' => $studentAttendance,
            'studentProgress' => $studentProgress,
        ]);
    }

    public function markLessonCompleted(Request $request, Course $course, Lesson $lesson)
    {
        /** @var User|null $student */
        $student = Auth::user();
        
        if (!$student instanceof User) {
            abort(403, __('You must be logged in to mark lessons as completed.'));
        }
        
        $student->refresh();
        $effectiveRole = $student->getEffectiveRole();

        if ($effectiveRole !== 'student' || !$student->isStudent()) {
            abort(403, __('Only students can mark lessons as completed.'));
        }

        $enrollments = Enrollment::where('student_id', $student->id)
            ->whereHas('batch', fn($q) => $q->where('course_id', $course->id))
            ->get();

        if ($enrollments->isEmpty()) {
            abort(403, __('You are not enrolled in this course.'));
        }

        $lesson->load('questions');
        
        if ($lesson->type === 'live') {
            $this->attendanceService->markAttendanceForLiveLesson($student, $course, $lesson);
            return response()->json([
                'message' => __('Attendance marked successfully for live meeting.'),
                'success' => true,
            ]);
        }
        
        $validation = $this->validateCompletionRequirements($lesson, $student, $request);
        if (!$validation['success']) {
            return response()->json($validation, 400);
        }

        // Mark attendance and completion
        $this->handleCompletion($student, $course, $lesson, $request);

        return response()->json([
            'message' => __('messages.lessons.marked_as_completed_successfully'),
            'success' => true,
        ]);
    }

    public function startLiveMeeting(Course $course, Lesson $lesson)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user instanceof User) {
            return response()->json([
                'success' => false,
                'message' => __('You must be logged in to start live meetings.'),
            ], 403);
        }
        
        if ($lesson->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => __('Lesson does not belong to this course.'),
            ], 400);
        }
        
        if ($lesson->type !== 'live') {
            return response()->json([
                'success' => false,
                'message' => __('This lesson is not a live meeting.'),
            ], 400);
        }
        
        if (!$lesson->live_meeting_date) {
            return response()->json([
                'success' => false,
                'message' => __('lessons.live.date_not_set'),
            ], 400);
        }
        
        $now = now();
        $meetingDate = \Illuminate\Support\Carbon::parse($lesson->live_meeting_date);
        $durationMinutes = $lesson->duration_minutes ?? 60;
        $meetingEndTime = $meetingDate->copy()->addMinutes($durationMinutes);
        
        if ($now->isAfter($meetingEndTime)) {
            return response()->json([
                'success' => false,
                'message' => __('lessons.live.meeting_already_ended'),
            ], 400);
        }
        
        $user->refresh();
        $effectiveRole = $user->getEffectiveRole();
        
        $isInstructor = $effectiveRole === 'instructor' && $user->isInstructor() && 
            Batch::where('course_id', $course->id)
                ->where('instructor_id', $user->id)
                ->exists();
        
        $isAdmin = $user->isAdmin();
        
        if (!$isInstructor && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => __('Only instructors or admins can start live meetings.'),
            ], 403);
        }
        
        DB::table('lessons')
            ->where('id', $lesson->id)
            ->update(['is_live_started' => 1]);
        
        return response()->json([
            'message' => __('Live meeting started successfully.'),
            'success' => true,
            'is_live_started' => true,
        ]);
    }

    private function validateAccess(?User $user, Course $course): void
    {
        if (!$user && !$course->is_published) {
            abort(404, __('messages.courses.not_published'));
        }

        if (!$user) return;

        $effectiveRole = $user->getEffectiveRole();

        if ($user->isAdmin()) return;

        if ($effectiveRole === 'instructor' && $user->isInstructor()) {
            if (Batch::where('course_id', $course->id)->where('instructor_id', $user->id)->exists()) {
                return;
            }
        }

        if ($effectiveRole === 'student' && $user->isStudent() && $user->canAccessCourse($course)) {
            return;
        }

        redirect()->route('courses.show', $course)
            ->with('error', __('messages.courses.must_enroll_first'))
            ->send();
        exit;
    }
    

    private function getCurrentLesson(?Lesson $lesson, $lessons): ?Lesson
    {
        if ($lesson) {
            $fullLesson = $this->lessonService->getLessonById($lesson->id);
            $fullLesson?->makeVisible(['video_url']);
            return $fullLesson;
        }

        return null;
    }

    private function getUserAnswers(?User $user, ?Lesson $lesson)
    {
        if (!$user || !$lesson) return collect();

        return UserQuestionAnswer::whereIn('question_id', $lesson->questions->pluck('id'))
            ->where('user_id', $user->id)
            ->with(['answer:id,answer,answer_ar'])
            ->get()
            ->keyBy('question_id');
    }

    private function getInstructorData(?User $user, Course $course, ?Lesson $lesson): array
    {
        $data = ['isInstructor' => false, 'batches' => collect(), 'students' => collect(), 'attendances' => collect()];

        $effectiveRole = $user->getEffectiveRole();
        
        if (!$user || $effectiveRole !== 'instructor' || !$user->isInstructor()) return $data;

        $batches = Batch::where('course_id', $course->id)
            ->where('instructor_id', $user->id)
            ->with(['enrollments.student'])
            ->get();

        if ($batches->isEmpty()) return $data;

        $data['isInstructor'] = true;
        $data['batches'] = $batches->map(fn($b) => ['id' => $b->id, 'name' => $b->translated_name]);
        
        $students = collect();
        foreach ($batches as $batch) {
            foreach ($batch->enrollments as $enrollment) {
                $students->push([
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'batch_id' => $batch->id,
                    'batch_name' => $batch->translated_name,
                ]);
            }
        }
        $data['students'] = $students->unique('id')->values();

        if ($lesson) {
            $data['attendances'] = $this->attendanceService->getLessonAttendances($lesson, $batches->pluck('id'));
        }

        return $data;
    }

    private function validateCompletionRequirements(Lesson $lesson, User $student, Request $request): array
    {
        $hasVideo = !empty($lesson->video_url);
        $hasQuestions = $lesson->questions && $lesson->questions->count() > 0;
        $isUploadedVideo = $lesson->type === 'video_file';
        $watchPercentage = $request->input('video_watch_percentage', 0);

        if ($hasVideo && $isUploadedVideo && $watchPercentage < 80) {
            return [
                'message' => __('You must watch at least 80% of the video to complete this lesson.'),
                'success' => false,
                'video_watch_percentage' => $watchPercentage,
            ];
        }

        if ($hasQuestions) {
            $answered = UserQuestionAnswer::where('user_id', $student->id)
                ->whereIn('question_id', $lesson->questions->pluck('id'))
                ->distinct('question_id')
                ->count('question_id');

            if ($answered < $lesson->questions->count()) {
                return [
                    'message' => __('You must answer all questions to complete this lesson.'),
                    'success' => false,
                    'answered_questions' => $answered,
                    'total_questions' => $lesson->questions->count(),
                ];
            }
        }

        return ['success' => true];
    }

    private function handleCompletion(User $student, Course $course, Lesson $lesson, Request $request): void
    {
        $hasVideo = !empty($lesson->video_url);
        $hasQuestions = $lesson->questions && $lesson->questions->count() > 0;
        $isUploadedVideo = $lesson->type === 'video_file';

        if ($hasVideo && $isUploadedVideo) {
            $this->attendanceService->markAttendanceAfterVideoWatch($student, $course, $lesson, $request->input('video_watch_percentage', 0));
        } elseif ($hasQuestions) {
            $this->attendanceService->markAttendanceAfterQuestions($student, $course, $lesson);
        }
    }

    private function formatCourse($course): array
    {
        return [
            'id' => $course->id,
            'slug' => $course->slug,
            'title' => $course->translated_title ?? $course->title,
            'title_ar' => $course->title_ar,
            'description' => $course->translated_description ?? $course->description,
            'description_ar' => $course->description_ar,
            'thumbnail_url' => $course->thumbnail_url,
        ];
    }

    private function formatLessonsList($lessons, ?User $user, Course $course): array
    {
        $enrollment = null;
        $effectiveRole = $user->getEffectiveRole();
        if ($user && $effectiveRole === 'student' && $user->isStudent()) {
            $enrollment = Enrollment::where('student_id', $user->id)
                ->whereHas('batch', fn($q) => $q->where('course_id', $course->id))
                ->first();
        }

        return $lessons->map(function ($lesson) use ($user, $enrollment, $effectiveRole) {
            $data = [
                'id' => $lesson->id,
                'type' => $lesson->type ?? 'video',
                'title' => $lesson->translated_title ?? $lesson->title,
                'duration_minutes' => $lesson->duration_minutes ?? 0,
                'order' => $lesson->order ?? 0,
            ];

            // Check effective role instead of user->role directly (supports multiple roles)
            if ($user && $effectiveRole === 'student' && $user->isStudent() && $enrollment) {
                $data['completed'] = $this->isLessonCompleted($lesson, $user);
                $data['attendance'] = $this->getLessonAttendanceStatus($lesson, $user, $enrollment->batch_id);
            }

            return $data;
        })->values()->toArray();
    }

    private function formatSectionsList($sections, ?User $user, Course $course): array
    {
        $enrollment = null;
        $effectiveRole = $user->getEffectiveRole();
        if ($user && $effectiveRole === 'student' && $user->isStudent()) {
            $enrollment = Enrollment::where('student_id', $user->id)
                ->whereHas('batch', fn($q) => $q->where('course_id', $course->id))
                ->first();
        }

        return $sections->map(function ($section) use ($user, $enrollment, $effectiveRole) {
            return [
                'id' => $section->id,
                'title' => $section->translated_title ?? $section->title,
                'order' => $section->order ?? 0,
                'lessons' => $section->lessons->map(function ($lesson) use ($user, $enrollment, $effectiveRole) {
                    $data = [
                        'id' => $lesson->id,
                        'type' => $lesson->type ?? 'video',
                        'title' => $lesson->translated_title ?? $lesson->title,
                        'duration_minutes' => $lesson->duration_minutes ?? 0,
                        'order' => $lesson->order ?? 0,
                    ];

                    if ($user && $effectiveRole === 'student' && $user->isStudent() && $enrollment) {
                        $data['completed'] = $this->isLessonCompleted($lesson, $user);
                        $data['attendance'] = $this->getLessonAttendanceStatus($lesson, $user, $enrollment->batch_id);
                    }

                    return $data;
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    private function getLessonAttendanceStatus(Lesson $lesson, User $user, int $batchId): ?string
    {
        $attendance = \App\Models\LessonAttendance::where('lesson_id', $lesson->id)
            ->where('student_id', $user->id)
            ->where('batch_id', $batchId)
            ->first();
        
        return $attendance?->status;
    }

    private function isLessonCompleted(Lesson $lesson, User $user): bool
    {
        // Get all enrollments for this student in this course
        $enrollments = Enrollment::where('student_id', $user->id)
            ->whereHas('batch', fn($q) => $q->where('course_id', $lesson->course_id))
            ->get();

        if ($enrollments->isEmpty()) {
            return false;
        }

        $batchIds = $enrollments->pluck('batch_id')->toArray();

        return LessonCompletion::where('lesson_id', $lesson->id)
            ->where('student_id', $user->id)
            ->whereIn('batch_id', $batchIds)
            ->exists();
    }
}

