<?php

namespace Modules\Courses\Policies;

use App\Models\User;
use App\Models\Batch;
use Modules\Courses\Models\Section;
use Modules\Courses\Models\Course;

class SectionPolicy
{
    public function view(User $user, Section $section): bool
    {
        $course = $section->course;

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
            // Check if instructor can create sections based on settings
            if (!\App\Models\Settings::instructorCanCreateSections()) {
                return false;
            }
            return $this->instructorTeachesCourse($user, $course);
        }

        return false;
    }

    public function update(User $user, Section $section): bool
    {
        return $this->create($user, $section->course);
    }

    public function delete(User $user, Section $section): bool
    {
        return $this->create($user, $section->course);
    }

    private function instructorTeachesCourse(User $user, Course $course): bool
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', $user->id)
            ->exists();
    }
}

