<?php

namespace Modules\Courses\Policies;

use App\Models\User;
use App\Models\Batch;
use Modules\Courses\Models\Question;
use Modules\Courses\Models\Lesson;
use Modules\Courses\Models\Course;

class QuestionPolicy
{
    public function view(User $user, Question $question): bool
    {
        $course = $question->lesson->course;

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

    public function create(User $user, Lesson $lesson): bool
    {
        $course = $lesson->course;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            // Check if instructor can create questions based on settings
            if (!\App\Models\Settings::instructorCanCreateQuestions()) {
                return false;
            }
            return $this->instructorTeachesCourse($user, $course);
        }

        return false;
    }

    public function update(User $user, Question $question): bool
    {
        return $this->create($user, $question->lesson);
    }

    public function delete(User $user, Question $question): bool
    {
        return $this->create($user, $question->lesson);
    }

    public function answer(User $user, Question $question): bool
    {
        if (!$user->isStudent()) {
            return false;
        }

        return $user->canAccessCourse($question->lesson->course);
    }

    private function instructorTeachesCourse(User $user, Course $course): bool
    {
        return Batch::where('course_id', $course->id)
            ->where('instructor_id', $user->id)
            ->exists();
    }
}

