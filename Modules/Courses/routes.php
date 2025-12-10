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
    Route::get('/courses', [AdminCourseController::class, 'index'])->middleware('permission:courses.view-all')->name('courses.index');
    Route::get('/courses/create', [AdminCourseController::class, 'create'])->middleware('permission:courses.create')->name('courses.create');
    Route::post('/courses', [AdminCourseController::class, 'store'])->middleware('permission:courses.create')->name('courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->middleware('permission:courses.view-all')->name('courses.show');
    Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->middleware('permission:courses.edit')->name('courses.edit');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->middleware('permission:courses.edit')->name('courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->middleware('permission:courses.delete')->name('courses.destroy');
    
    // Course Content Management (Sections, Lessons, Questions)
    Route::prefix('courses/{course}')->name('courses.')->group(function () {
        
        // Batches Management
        Route::get('/batches', [AdminBatchController::class, 'index'])->middleware('permission:batches.view')->name('batches.index');
        Route::get('/batches/create', [AdminBatchController::class, 'create'])->middleware('permission:batches.create')->name('batches.create');
        Route::post('/batches', [AdminBatchController::class, 'store'])->middleware('permission:batches.create')->name('batches.store');
        Route::get('/batches/{batch}', [AdminBatchController::class, 'show'])->middleware('permission:batches.view')->name('batches.show');
        Route::get('/batches/{batch}/edit', [AdminBatchController::class, 'edit'])->middleware('permission:batches.edit')->name('batches.edit');
        Route::put('/batches/{batch}', [AdminBatchController::class, 'update'])->middleware('permission:batches.edit')->name('batches.update');
        Route::delete('/batches/{batch}', [AdminBatchController::class, 'destroy'])->middleware('permission:batches.delete')->name('batches.destroy');
        Route::post('/batches/{batch}/students', [AdminBatchController::class, 'addStudents'])->middleware('permission:batches.manage')->name('batches.add-students');
        Route::delete('/batches/{batch}/students/{student}', [AdminBatchController::class, 'removeStudent'])->middleware('permission:batches.manage')->name('batches.remove-student');
        
        // Sections Management
        Route::get('/sections', [AdminSectionController::class, 'index'])->middleware('permission:sections.view')->name('sections.index');
        Route::get('/sections/create', [AdminSectionController::class, 'create'])->middleware('permission:sections.create')->name('sections.create');
        Route::post('/sections', [AdminSectionController::class, 'store'])->middleware('permission:sections.create')->name('sections.store');
        Route::get('/sections/{section}/edit', [AdminSectionController::class, 'edit'])->middleware('permission:sections.edit')->name('sections.edit');
        Route::put('/sections/{section}', [AdminSectionController::class, 'update'])->middleware('permission:sections.edit')->name('sections.update');
        Route::delete('/sections/{section}', [AdminSectionController::class, 'destroy'])->middleware('permission:sections.delete')->name('sections.destroy');
        Route::post('/sections/reorder', [AdminSectionController::class, 'reorder'])->middleware('permission:sections.edit')->name('sections.reorder');
        
        // Lessons Management
        Route::get('/lessons', [AdminLessonController::class, 'index'])->middleware('permission:lessons.view')->name('lessons.index');
        Route::get('/lessons/create', [AdminLessonController::class, 'create'])->middleware('permission:lessons.create')->name('lessons.create');
        Route::post('/lessons', [AdminLessonController::class, 'store'])->middleware('permission:lessons.create')->name('lessons.store');
        Route::get('/lessons/{lesson}', [AdminLessonController::class, 'show'])->middleware('permission:lessons.view')->name('lessons.show');
        Route::get('/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->middleware('permission:lessons.edit')->name('lessons.edit');
        Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update'])->middleware('permission:lessons.edit')->name('lessons.update');
        Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->middleware('permission:lessons.delete')->name('lessons.destroy');
        Route::post('/lessons/reorder', [AdminLessonController::class, 'reorder'])->middleware('permission:lessons.edit')->name('lessons.reorder');
        Route::post('/lessons/{lesson}/attendance', [AdminLessonController::class, 'markAttendance'])->middleware('permission:attendance.mark')->name('lessons.attendance.mark');
        Route::get('/lessons/{lesson}/attendance', [AdminLessonController::class, 'attendanceReport'])->middleware('permission:attendance.view')->name('lessons.attendance.report');
        
        // Questions Management (nested under lessons)
        Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
            Route::get('/questions', [AdminQuestionController::class, 'index'])->middleware('permission:questions.view')->name('questions.index');
            Route::get('/questions/create', [AdminQuestionController::class, 'create'])->middleware('permission:questions.create')->name('questions.create');
            Route::post('/questions', [AdminQuestionController::class, 'store'])->middleware('permission:questions.create')->name('questions.store');
            Route::get('/questions/{question}', [AdminQuestionController::class, 'show'])->middleware('permission:questions.view')->name('questions.show');
            Route::get('/questions/{question}/edit', [AdminQuestionController::class, 'edit'])->middleware('permission:questions.edit')->name('questions.edit');
            Route::put('/questions/{question}', [AdminQuestionController::class, 'update'])->middleware('permission:questions.edit')->name('questions.update');
            Route::delete('/questions/{question}', [AdminQuestionController::class, 'destroy'])->middleware('permission:questions.delete')->name('questions.destroy');
            Route::post('/questions/reorder', [AdminQuestionController::class, 'reorder'])->middleware('permission:questions.edit')->name('questions.reorder');
        });
    });
});

// ============================================
// Instructor Routes
// ============================================
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    
    // Batches (read-only)
    Route::get('/batches', [InstructorBatchController::class, 'index'])->middleware('permission:batches.view')->name('batches.index');
    Route::get('/batches/{batch}', [InstructorBatchController::class, 'show'])->middleware('permission:batches.view')->name('batches.show');
    
    // Course Content Management
    Route::prefix('courses/{course}')->group(function () {
        
        // Sections
        Route::get('/sections', [InstructorSectionController::class, 'index'])->middleware('permission:sections.view')->name('sections.index');
        Route::post('/sections', [InstructorSectionController::class, 'store'])->middleware('permission:sections.create')->name('sections.store');
        Route::put('/sections/{section}', [InstructorSectionController::class, 'update'])->middleware('permission:sections.edit')->name('sections.update');
        Route::delete('/sections/{section}', [InstructorSectionController::class, 'destroy'])->middleware('permission:sections.delete')->name('sections.destroy');
        Route::post('/sections/reorder', [InstructorSectionController::class, 'reorder'])->middleware('permission:sections.edit')->name('sections.reorder');

        // Lessons
        Route::get('/lessons', [InstructorLessonController::class, 'index'])->middleware('permission:lessons.view')->name('lessons.index');
        Route::get('/lessons/create', [InstructorLessonController::class, 'create'])->middleware('permission:lessons.create')->name('lessons.create');
        Route::post('/lessons', [InstructorLessonController::class, 'store'])->middleware('permission:lessons.create')->name('lessons.store');
        Route::get('/lessons/{lesson}', [InstructorLessonController::class, 'show'])->middleware('permission:lessons.view')->name('lessons.show');
        Route::get('/lessons/{lesson}/edit', [InstructorLessonController::class, 'edit'])->middleware('permission:lessons.edit')->name('lessons.edit');
        Route::put('/lessons/{lesson}', [InstructorLessonController::class, 'update'])->middleware('permission:lessons.edit')->name('lessons.update');
        Route::delete('/lessons/{lesson}', [InstructorLessonController::class, 'destroy'])->middleware('permission:lessons.delete')->name('lessons.destroy');
        
        // Questions
        Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
            Route::get('/questions', [InstructorQuestionController::class, 'index'])->middleware('permission:questions.view')->name('questions.index');
            Route::get('/questions/create', [InstructorQuestionController::class, 'create'])->middleware('permission:questions.create')->name('questions.create');
            Route::post('/questions', [InstructorQuestionController::class, 'store'])->middleware('permission:questions.create')->name('questions.store');
            Route::get('/questions/{question}', [InstructorQuestionController::class, 'show'])->middleware('permission:questions.view')->name('questions.show');
            Route::delete('/questions/{question}', [InstructorQuestionController::class, 'destroy'])->middleware('permission:questions.delete')->name('questions.destroy');
            Route::post('/questions/reorder', [InstructorQuestionController::class, 'reorder'])->middleware('permission:questions.edit')->name('questions.reorder');
        });
        
        // Attendance
        Route::post('/lessons/{lesson}/attendance', [InstructorLessonController::class, 'markAttendance'])->middleware('permission:attendance.mark')->name('lessons.attendance.mark');
    });
});

// ============================================
// Student Routes
// ============================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/courses', [StudentCourseController::class, 'index'])->middleware('permission:courses.view')->name('courses.index');
    
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->middleware('permission:lessons.view')->name('assignments.index');
    Route::get('/assignments/{lesson}', [StudentAssignmentController::class, 'show'])->middleware('permission:lessons.view')->name('assignments.show');
    
    Route::get('/tests', [StudentTestController::class, 'index'])->middleware('permission:lessons.view')->name('tests.index');
    Route::get('/tests/{lesson}', [StudentTestController::class, 'show'])->middleware('permission:lessons.view')->name('tests.show');
    
    Route::post('/lessons/{lesson}/questions/{question}/answers', [StudentQuestionAnswerController::class, 'store'])->middleware('permission:questions.view')->name('question-answers.store');
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
    Route::get('/courses/create', [CourseController::class, 'create'])->middleware('permission:courses.create')->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->middleware('permission:courses.create')->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->middleware('permission:courses.edit')->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware('permission:courses.edit')->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->middleware('permission:courses.delete')->name('courses.destroy');
    
    // Lessons (general)
    Route::prefix('courses/{course}')->name('lessons.')->group(function () {
        Route::get('/lessons', [LessonController::class, 'index'])->middleware('permission:lessons.view')->name('index');
        Route::get('/lessons/create', [LessonController::class, 'create'])->middleware('permission:lessons.create')->name('create');
        Route::post('/lessons', [LessonController::class, 'store'])->middleware('permission:lessons.create')->name('store');
        Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->middleware('permission:lessons.view')->name('show');
        Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->middleware('permission:lessons.edit')->name('edit');
        Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->middleware('permission:lessons.edit')->name('update');
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->middleware('permission:lessons.delete')->name('destroy');
    });
});

// ============================================
// Public Routes
// ============================================
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
