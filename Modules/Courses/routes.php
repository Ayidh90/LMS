<?php

use Illuminate\Support\Facades\Route;
use Modules\Courses\Controllers\CourseController;
use Modules\Courses\Controllers\LessonController;
use Modules\Courses\Controllers\CoursePlayerController;
use Modules\Courses\Controllers\Admin\CourseController as AdminCourseController;
use Modules\Courses\Controllers\Admin\BatchController as AdminBatchController;
use Modules\Courses\Controllers\Admin\SectionController as AdminSectionController;
use Modules\Courses\Controllers\Admin\LessonController as AdminLessonController;
use Modules\Courses\Controllers\Admin\QuestionController as AdminQuestionController;
use Modules\Courses\Controllers\Instructor\LessonController as InstructorLessonController;
use Modules\Courses\Controllers\Instructor\SectionController as InstructorSectionController;
use Modules\Courses\Controllers\Instructor\QuestionController as InstructorQuestionController;
use Modules\Courses\Controllers\Instructor\BatchController as InstructorBatchController;
use Modules\Courses\Controllers\Student\CourseController as StudentCourseController;
use Modules\Courses\Controllers\Student\AssignmentController as StudentAssignmentController;
use Modules\Courses\Controllers\Student\TestController as StudentTestController;
use Modules\Courses\Controllers\Student\QuestionAnswerController as StudentQuestionAnswerController;

/*
|--------------------------------------------------------------------------
| Courses Module Routes
|--------------------------------------------------------------------------
*/

// ============================================
// Admin Routes (Full Course Management)
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Courses CRUD
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');
    
    // Course Content Management (Sections, Lessons, Questions)
    Route::prefix('courses/{course}')->name('courses.')->group(function () {
        
        // Batches Management
        Route::get('/batches', [AdminBatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/create', [AdminBatchController::class, 'create'])->name('batches.create');
        Route::post('/batches', [AdminBatchController::class, 'store'])->name('batches.store');
        Route::get('/batches/{batch}', [AdminBatchController::class, 'show'])->name('batches.show');
        Route::get('/batches/{batch}/edit', [AdminBatchController::class, 'edit'])->name('batches.edit');
        Route::put('/batches/{batch}', [AdminBatchController::class, 'update'])->name('batches.update');
        Route::delete('/batches/{batch}', [AdminBatchController::class, 'destroy'])->name('batches.destroy');
        Route::post('/batches/{batch}/students', [AdminBatchController::class, 'addStudents'])->name('batches.add-students');
        Route::delete('/batches/{batch}/students/{student}', [AdminBatchController::class, 'removeStudent'])->name('batches.remove-student');
        
        // Sections Management
        Route::get('/sections', [AdminSectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/create', [AdminSectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [AdminSectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [AdminSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [AdminSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [AdminSectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/reorder', [AdminSectionController::class, 'reorder'])->name('sections.reorder');
        
        // Lessons Management
        Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/create', [AdminLessonController::class, 'create'])->name('lessons.create');
        Route::post('/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}', [AdminLessonController::class, 'show'])->name('lessons.show');
        Route::get('/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
        Route::post('/lessons/reorder', [AdminLessonController::class, 'reorder'])->name('lessons.reorder');
        Route::post('/lessons/{lesson}/attendance', [AdminLessonController::class, 'markAttendance'])->name('lessons.attendance.mark');
        Route::get('/lessons/{lesson}/attendance', [AdminLessonController::class, 'attendanceReport'])->name('lessons.attendance.report');
        
        // Questions Management (nested under lessons)
        Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
            Route::get('/questions', [AdminQuestionController::class, 'index'])->name('questions.index');
            Route::get('/questions/create', [AdminQuestionController::class, 'create'])->name('questions.create');
            Route::post('/questions', [AdminQuestionController::class, 'store'])->name('questions.store');
            Route::get('/questions/{question}', [AdminQuestionController::class, 'show'])->name('questions.show');
            Route::get('/questions/{question}/edit', [AdminQuestionController::class, 'edit'])->name('questions.edit');
            Route::put('/questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
            Route::delete('/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');
            Route::post('/questions/reorder', [AdminQuestionController::class, 'reorder'])->name('questions.reorder');
        });
    });
});

// ============================================
// Instructor Routes
// ============================================
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    
    // Batches (read-only)
    Route::get('/batches', [InstructorBatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/{batch}', [InstructorBatchController::class, 'show'])->name('batches.show');
    
    // Course Content Management
    Route::prefix('courses/{course}')->group(function () {
        
        // Sections
        Route::get('/sections', [InstructorSectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [InstructorSectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [InstructorSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [InstructorSectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/reorder', [InstructorSectionController::class, 'reorder'])->name('sections.reorder');

        // Lessons
        Route::get('/lessons', [InstructorLessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/create', [InstructorLessonController::class, 'create'])->name('lessons.create');
        Route::post('/lessons', [InstructorLessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}', [InstructorLessonController::class, 'show'])->name('lessons.show');
        Route::get('/lessons/{lesson}/edit', [InstructorLessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{lesson}', [InstructorLessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [InstructorLessonController::class, 'destroy'])->name('lessons.destroy');
        
        // Questions
        Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
            Route::get('/questions', [InstructorQuestionController::class, 'index'])->name('questions.index');
            Route::get('/questions/create', [InstructorQuestionController::class, 'create'])->name('questions.create');
            Route::post('/questions', [InstructorQuestionController::class, 'store'])->name('questions.store');
            Route::get('/questions/{question}', [InstructorQuestionController::class, 'show'])->name('questions.show');
            Route::delete('/questions/{question}', [InstructorQuestionController::class, 'destroy'])->name('questions.destroy');
            Route::post('/questions/reorder', [InstructorQuestionController::class, 'reorder'])->name('questions.reorder');
        });
        
        // Attendance
        Route::post('/lessons/{lesson}/attendance', [InstructorLessonController::class, 'markAttendance'])->name('lessons.attendance.mark');
    });
});

// ============================================
// Student Routes
// ============================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{lesson}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    
    Route::get('/tests', [StudentTestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{lesson}', [StudentTestController::class, 'show'])->name('tests.show');
    
    Route::post('/lessons/{lesson}/questions/{question}/answers', [StudentQuestionAnswerController::class, 'store'])->name('question-answers.store');
});

// ============================================
// Authenticated Course Routes
// ============================================
Route::middleware('auth')->group(function () {
    // Course Player
    Route::get('/courses/{course}/play', [CoursePlayerController::class, 'show'])->name('courses.play');
    Route::get('/courses/{course}/play/{lesson}', [CoursePlayerController::class, 'show'])->name('courses.play.lesson');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [CoursePlayerController::class, 'markLessonCompleted'])->name('lessons.complete');
    
    // Course CRUD (general - redirects admins)
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    
    // Lessons (general)
    Route::prefix('courses/{course}')->name('lessons.')->group(function () {
        Route::get('/lessons', [LessonController::class, 'index'])->name('index');
        Route::get('/lessons/create', [LessonController::class, 'create'])->name('create');
        Route::post('/lessons', [LessonController::class, 'store'])->name('store');
        Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('show');
        Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('edit');
        Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('update');
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('destroy');
    });
});

// ============================================
// Public Routes
// ============================================
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
