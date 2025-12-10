<?php

namespace Modules\Courses\Policies;

use App\Models\User;
use App\Models\Batch;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Course;

class LessonPolicy
{
    public function view(User $user, Lesson $lesson): bool
    {
        $course = $lesson->course;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            return $this->instructorTeachesCourse($user, $course);
        }

        if ($user->isStudent()) {
            return $user->canAccessCourse($course);
        }

        return false;
    }

    public function create(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            // Check if instructor can create lessons based on settings
            if (!\App\Models\Settings::instructorCanCreateLessons()) {
                return false;
            }
            return $this->instructorTeachesCourse($user, $course);
        }

        return false;
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $this->create($user, $lesson->course);
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $this->create($user, $lesson->course);
    }

    public function complete(User $user, Lesson $lesson): bool
    {
        if (!$user->isStudent()) {
            return false;
        }

        return $user->canAccessCourse($lesson->course);
    }

    private function instructorTeachesCourse(User $user, Course $course): bool
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', $user->id)
            ->exists();
    }
}

