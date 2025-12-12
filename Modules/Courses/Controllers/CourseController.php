<?php

namespace Modules\Courses\Controllers;

use App\Http\Controllers\Controller;
use Modules\Courses\Models\Course;
use Modules\Courses\Requests\StoreCourseRequest;
use Modules\Courses\Requests\UpdateCourseRequest;
use Modules\Courses\Services\CourseService;
use Modules\Courses\Services\FavoriteService;
use Modules\Courses\Services\LessonAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService,
        private FavoriteService $favoriteService,
        private LessonAttendanceService $attendanceService
    ) {}

    public function create()
    {
        $this->authorize('create', Course::class);
        
        $user = Auth::user();
        // Redirect admins to admin dashboard course creation
        if ($user && ($user->is_admin || $user->role === 'admin' || $user->role === 'super_admin')) {
            return redirect()->route('admin.courses.create');
        }
        
        return Inertia::render('Courses/Create');
    }

    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validated();
        $thumbnailFile = $request->file('thumbnail');

        $course = $this->courseService->createCourse([
            ...$validated,
            'instructor_id' => Auth::id(),
            'category_id' => $validated['category_id'] ?? null,
            'is_published' => false,
        ], $thumbnailFile);

        $user = Auth::user();
        // Redirect admins to admin dashboard after creation
        if ($user && ($user->is_admin || $user->role === 'admin' || $user->role === 'super_admin')) {
            return redirect()->route('admin.courses.index')->with('success', __('messages.courses.created_successfully'));
        }

        return redirect()->route('courses.show', $course)->with('success', __('messages.courses.created_successfully'));
    }

    public function show(Course $course)
    {
        $user = Auth::user();
        
        // Validate course access
        if (!$user && !$course->is_published) {
            abort(404, __('messages.courses.not_found'));
        }

        $courseData = $this->courseService->getCourseById($course->id);
        if (!$courseData) {
            abort(404, __('messages.courses.not_found'));
        }

        // Load sections and lessons for the course
        $courseData->load([
            'sections' => function($query) {
                $query->orderBy('order');
            },
            'sections.lessons' => function($query) {
                $query->orderBy('order');
            },
            'lessons' => function($query) {
                $query->orderBy('order');
            }
        ]);

        // Make translated fields visible for sections and lessons
        if ($courseData->sections) {
            foreach ($courseData->sections as $section) {
                $section->makeVisible(['translated_title', 'translated_description']);
                if ($section->lessons) {
                    foreach ($section->lessons as $lesson) {
                        $lesson->makeVisible(['translated_title', 'translated_description']);
                    }
                }
            }
        }
        if ($courseData->lessons) {
            foreach ($courseData->lessons as $lesson) {
                $lesson->makeVisible(['translated_title', 'translated_description']);
            }
        }

        // User permissions and status
        $canEdit = $user && $user->isAdmin();
        $isEnrolled = $user && $user->isStudent() 
            && $user->enrolledCourses()->where('courses.id', $courseData->id)->exists();
        $isFavorited = $user ? $this->favoriteService->isFavorited($courseData) : false;
        
        // Check if user is instructor through batches
        $isInstructor = false;
        $instructorBatches = collect();
        if ($user && $user->isInstructor()) {
            $instructorBatches = \App\Models\Batch::where('course_id', $course->id)
                ->where('instructor_id', $user->id)
                ->get();
            $isInstructor = $instructorBatches->isNotEmpty();
        }
        
        // Get batches for current user
        $courseBatches = $this->getCourseBatches($course, $user, $isInstructor, $instructorBatches);
        
        // Get instructor dashboard data
        $instructorData = $isInstructor 
            ? $this->getInstructorData($course, $user) 
            : null;

        // Format course data with additional information
        $formattedCourse = $this->formatCourseForShow($courseData, $course);
        
        // Get all students enrolled in this course with their batch information
        $courseStudents = $this->getCourseStudents($course);

        return Inertia::render('Courses/Show', [
            'course' => $formattedCourse,
            'canEdit' => $canEdit,
            'isEnrolled' => $isEnrolled,
            'isFavorited' => $isFavorited,
            'isInstructor' => $isInstructor,
            'instructorData' => $instructorData,
            'batches' => $courseBatches,
            'courseStudents' => $courseStudents,
        ]);
    }

    /**
     * Get batches for course based on user role
     */
    private function getCourseBatches(Course $course, ?\App\Models\User $user, bool $isInstructor, $instructorBatches)
    {
        if (!$user) {
            return null;
        }

        if ($user->isStudent()) {
            return \App\Models\Batch::where('course_id', $course->id)
                ->whereHas('enrollments', fn($q) => $q->where('student_id', $user->id))
                ->with('instructor:id,name,email')
                ->get()
                ->map(fn($batch) => $this->formatBatchForStudent($batch));
        }

        if ($isInstructor) {
            return $instructorBatches->map(fn($batch) => $this->formatBatchForInstructor($batch));
        }

        return null;
    }

    /**
     * Format batch data for student view
     */
    private function formatBatchForStudent(\App\Models\Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'instructor' => $batch->instructor ? [
                'id' => $batch->instructor->id,
                'name' => $batch->instructor->name,
                'email' => $batch->instructor->email,
            ] : null,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
        ];
    }

    /**
     * Format batch data for instructor view
     */
    private function formatBatchForInstructor(\App\Models\Batch $batch): array
    {
        return [
            'id' => $batch->id,
            'name' => $batch->translated_name,
            'description' => $batch->translated_description,
            'start_date' => $batch->start_date,
            'end_date' => $batch->end_date,
            'is_active' => $batch->is_active,
            'enrollments_count' => $batch->enrollments()->count(),
        ];
    }

    /**
     * Get instructor dashboard data (students, lessons, statistics)
     */
    private function getInstructorData(Course $course, \App\Models\User $instructor): array
    {
        // Get enrolled students from instructor's batches
        $enrollments = \App\Models\Enrollment::whereHas('batch', function($query) use ($course, $instructor) {
                $query->where('course_id', $course->id)
                      ->where('instructor_id', $instructor->id);
            })
            ->with(['student', 'batch'])
            ->orderBy('enrolled_at', 'desc')
            ->get()
            ->map(fn($e) => $this->formatEnrollment($e));
        
        // Get lessons with questions
        $lessons = $course->lessons()
            ->with(['questions.answers'])
            ->orderBy('order')
            ->get()
            ->map(fn($lesson) => $this->formatLesson($lesson));
        
        // Calculate statistics
        $statistics = [
            'total_students' => $enrollments->count(),
            'completed_students' => $enrollments->where('status', 'completed')->count(),
            'average_progress' => round($enrollments->avg('progress') ?? 0, 1),
            'total_lessons' => $lessons->count(),
            'total_questions' => $lessons->sum('questions_count'),
        ];
        
        return [
            'students' => $enrollments,
            'lessons' => $lessons,
            'statistics' => $statistics,
        ];
    }

    /**
     * Format enrollment data
     */
    private function formatEnrollment(\App\Models\Enrollment $enrollment): array
    {
        // Calculate actual progress from LessonCompletion records
        $course = $enrollment->batch->course;
        
        // Sync LessonCompletion records from LessonAttendance first
        $this->attendanceService->syncCompletionsFromAttendance($enrollment->student, $course, $enrollment->batch_id);
        
        $totalLessons = $course->lessons()->count();
        $completedLessons = \App\Models\LessonCompletion::where('student_id', $enrollment->student_id)
            ->where('batch_id', $enrollment->batch_id)
            ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
            ->count();
        
        $calculatedProgress = $totalLessons > 0 
            ? (int) round(($completedLessons / $totalLessons) * 100) 
            : 0;
        
        // Use calculated progress if enrollment progress is outdated or null
        $progress = $enrollment->progress ?? $calculatedProgress;
        
        // Update enrollment progress if it's different (async update)
        if ($progress !== $calculatedProgress) {
            $enrollment->update(['progress' => $calculatedProgress]);
            $progress = $calculatedProgress;
        }
        
        return [
            'id' => $enrollment->student->id,
            'name' => $enrollment->student->name,
            'email' => $enrollment->student->email,
            'enrolled_at' => $enrollment->enrolled_at,
            'progress' => $progress,
            'status' => $enrollment->status,
            'completed_at' => $enrollment->completed_at,
            'batch' => [
                'id' => $enrollment->batch->id,
                'name' => $enrollment->batch->translated_name ?? $enrollment->batch->name,
                'start_date' => $enrollment->batch->start_date,
                'end_date' => $enrollment->batch->end_date,
            ],
        ];
    }

    /**
     * Format lesson data with questions
     */
    private function formatLesson(\Modules\Courses\Models\Lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'type' => $lesson->type ?? 'video',
            'title' => $lesson->translated_title ?? $lesson->title,
            'title_ar' => $lesson->title_ar,
            'order' => $lesson->order,
            'duration_minutes' => $lesson->duration_minutes,
            'is_free' => $lesson->is_free,
            'questions_count' => $lesson->questions->count(),
            'questions' => $lesson->questions->map(fn($q) => $this->formatQuestion($q)),
        ];
    }

    /**
     * Format question data with answers
     */
    private function formatQuestion(\Modules\Courses\Models\Question $question): array
    {
        return [
            'id' => $question->id,
            'type' => $question->type,
            'question' => $question->translated_question ?? $question->question,
            'question_ar' => $question->question_ar,
            'points' => $question->points,
            'answers_count' => $question->answers->count(),
            'answers' => $question->answers->map(fn($a) => [
                'id' => $a->id,
                'answer' => $a->translated_answer ?? $a->answer,
                'answer_ar' => $a->answer_ar,
                'is_correct' => $a->is_correct,
            ]),
        ];
    }

    /**
     * Get all students enrolled in course with their batch information
     */
    private function getCourseStudents(Course $course): array
    {
        $enrollments = \App\Models\Enrollment::whereHas('batch', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with(['student', 'batch.instructor:id,name,email'])
            ->orderBy('enrolled_at', 'desc')
            ->get();
        
        return $enrollments->map(function($enrollment) {
            return [
                'id' => $enrollment->student->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
                'batch' => [
                    'id' => $enrollment->batch->id,
                    'name' => $enrollment->batch->translated_name ?? $enrollment->batch->name,
                    'start_date' => $enrollment->batch->start_date,
                    'end_date' => $enrollment->batch->end_date,
                    'instructor' => $enrollment->batch->instructor ? [
                        'id' => $enrollment->batch->instructor->id,
                        'name' => $enrollment->batch->instructor->name,
                    ] : null,
                ],
                'enrolled_at' => $enrollment->enrolled_at,
                'progress' => $enrollment->progress ?? 0,
                'status' => $enrollment->status ?? 'enrolled',
            ];
        })->values()->toArray();
    }

    /**
     * Format course data for show page with instructor and students count
     */
    private function formatCourseForShow($courseData, Course $course): array
    {
        // Get instructor from first batch (or any batch)
        $firstBatch = \App\Models\Batch::where('course_id', $course->id)
            ->with('instructor:id,name,email')
            ->first();
        
        $instructor = null;
        if ($firstBatch && $firstBatch->instructor) {
            $instructor = [
                'id' => $firstBatch->instructor->id,
                'name' => $firstBatch->instructor->name,
                'email' => $firstBatch->instructor->email,
            ];
        }

        // Calculate students count from enrollments through batches
        $studentsCount = \App\Models\Enrollment::whereHas('batch', function($query) use ($course) {
            $query->where('course_id', $course->id);
        })->distinct()->count('student_id');

        // Convert course to array and add additional fields
        $courseArray = $courseData->toArray();
        
        // Ensure sections and lessons are properly included
        if ($courseData->sections) {
            $courseArray['sections'] = $courseData->sections->map(function($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->translated_title ?? $section->title,
                    'title_ar' => $section->title_ar,
                    'description' => $section->translated_description ?? $section->description,
                    'description_ar' => $section->description_ar,
                    'order' => $section->order,
                    'lessons' => $section->lessons ? $section->lessons->map(function($lesson) {
                        return [
                            'id' => $lesson->id,
                            'title' => $lesson->translated_title ?? $lesson->title,
                            'title_ar' => $lesson->title_ar,
                            'type' => $lesson->type ?? 'video',
                            'duration_minutes' => $lesson->duration_minutes ?? 0,
                            'order' => $lesson->order ?? 0,
                        ];
                    })->values()->toArray() : [],
                ];
            })->values()->toArray();
        }
        
        if ($courseData->lessons) {
            $courseArray['lessons'] = $courseData->lessons->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->translated_title ?? $lesson->title,
                    'title_ar' => $lesson->title_ar,
                    'type' => $lesson->type ?? 'video',
                    'duration_minutes' => $lesson->duration_minutes ?? 0,
                    'order' => $lesson->order ?? 0,
                ];
            })->values()->toArray();
        }
        
        // Add instructor if found
        if ($instructor) {
            $courseArray['instructor'] = $instructor;
        }

        // Update students_count (use calculated value if database value is 0 or null)
        $courseArray['students_count'] = $studentsCount > 0 ? $studentsCount : ($courseArray['students_count'] ?? 0);

        // Ensure level and duration_hours are included
        $courseArray['level'] = $courseArray['level'] ?? $course->level ?? null;
        $courseArray['duration_hours'] = $courseArray['duration_hours'] ?? $course->duration_hours ?? 0;

        return $courseArray;
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);

        // Ensure thumbnail_url and translated fields are visible (they're already appended)
        $course->makeVisible(['thumbnail_url', 'translated_title', 'translated_description']);

        return Inertia::render('Courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validated();
        $thumbnailFile = $request->file('thumbnail');

        $this->courseService->updateCourse($course, $validated, $thumbnailFile);

        return redirect()->route('courses.show', $course)->with('success', __('messages.courses.updated_successfully'));
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        $this->courseService->deleteCourse($course);

        return redirect()->route('profile.show')->with('success', __('messages.courses.deleted_successfully'));
    }
}

