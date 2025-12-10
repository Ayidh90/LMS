<?php

namespace Modules\Courses\Policies;

use App\Models\User;
use App\Models\Batch;
use Modules\Courses\Models\Course;

class CoursePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        // Published courses are viewable by everyone
        if ($course->is_published) {
            return true;
        }

        // Unpublished courses require authentication
        if (!$user) {
            return false;
        }

        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view courses they teach
        if ($user->isInstructor()) {
            return $this->instructorTeachesCourse($user, $course);
        }

        // Student can view enrolled courses
        if ($user->isStudent()) {
            return $user->canAccessCourse($course);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function viewLessons(User $user, Course $course): bool
    {
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

    public function createLesson(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            return $this->instructorTeachesCourse($user, $course);
        }

        return false;
    }

    public function updateLesson(User $user, Course $course): bool
    {
        return $this->createLesson($user, $course);
    }

    public function deleteLesson(User $user, Course $course): bool
    {
        return $this->createLesson($user, $course);
    }

    public function markAttendance(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            return $this->instructorTeachesCourse($user, $course);
        }

        return false;
    }

    public function manageBatches(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    private function instructorTeachesCourse(User $user, Course $course): bool
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', $user->id)
            ->exists();
    }
}
