<?php

namespace App\Providers;

use Modules\Courses\Models\Course;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Section;
use Modules\Courses\Models\Question;
use Modules\Courses\Policies\CoursePolicy;
use Modules\Courses\Policies\LessonPolicy;
use Modules\Courses\Policies\SectionPolicy;
use Modules\Courses\Policies\QuestionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        Lesson::class => LessonPolicy::class,
        Section::class => SectionPolicy::class,
        Question::class => QuestionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Course Gates
        Gate::define('viewLessons', [CoursePolicy::class, 'viewLessons']);
        Gate::define('createLesson', [CoursePolicy::class, 'createLesson']);
        Gate::define('updateLesson', [CoursePolicy::class, 'updateLesson']);
        Gate::define('deleteLesson', [CoursePolicy::class, 'deleteLesson']);
        Gate::define('markAttendance', [CoursePolicy::class, 'markAttendance']);
        Gate::define('manageBatches', [CoursePolicy::class, 'manageBatches']);
    }
}
