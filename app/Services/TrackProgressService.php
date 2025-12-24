<?php

namespace App\Services;

use App\Models\TrackProgress;
use App\Models\TrackCourseProgress;
use App\Models\Enrollment;
use App\Models\User;
use Modules\Courses\Models\Course;
use App\Models\Track;

class TrackProgressService
{
    /**
     * Initialize or update track progress for a student
     */
    public function initializeTrackProgress(User $student, Track $track): TrackProgress
    {
        $trackProgress = TrackProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'track_id' => $track->id,
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );

        // Initialize course progress for all courses in the track
        $courses = $track->courses;
        foreach ($courses as $course) {
            TrackCourseProgress::firstOrCreate(
                [
                    'track_progress_id' => $trackProgress->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => 'not_started',
                ]
            );
        }

        return $trackProgress;
    }

    /**
     * Update course progress in track when enrollment progress changes
     */
    public function updateCourseProgressInTrack(Enrollment $enrollment): void
    {
        $course = $enrollment->course();
        if (!$course || !$course->track_id) {
            return;
        }

        $track = Track::find($course->track_id);
        if (!$track) {
            return;
        }

        $trackProgress = TrackProgress::where('student_id', $enrollment->student_id)
            ->where('track_id', $track->id)
            ->first();

        if (!$trackProgress) {
            $trackProgress = $this->initializeTrackProgress($enrollment->student, $track);
        }

        // Update or create course progress
        $courseProgress = TrackCourseProgress::firstOrCreate(
            [
                'track_progress_id' => $trackProgress->id,
                'course_id' => $course->id,
            ]
        );

        $courseProgress->enrollment_id = $enrollment->id;
        $courseProgress->updateFromEnrollment();
    }

    /**
     * Get track progress for a student
     */
    public function getStudentTrackProgress(User $student, Track $track): ?array
    {
        $trackProgress = TrackProgress::where('student_id', $student->id)
            ->where('track_id', $track->id)
            ->with(['courseProgress.course', 'courseProgress.enrollment'])
            ->first();

        if (!$trackProgress) {
            return null;
        }

        // Ensure all courses have progress records
        $courses = $track->courses;
        foreach ($courses as $course) {
            $courseProgress = TrackCourseProgress::firstOrCreate(
                [
                    'track_progress_id' => $trackProgress->id,
                    'course_id' => $course->id,
                ]
            );

            // Update from enrollment if exists
            if ($courseProgress->enrollment) {
                $courseProgress->updateFromEnrollment();
            }
        }

        // Recalculate track progress
        $trackProgress->calculateProgress();
        $trackProgress->refresh();

        return [
            'track_progress' => $trackProgress,
            'course_progress' => $trackProgress->courseProgress->map(function ($cp) {
                return [
                    'course_id' => $cp->course_id,
                    'course_name' => $cp->course->translated_title ?? $cp->course->title,
                    'progress' => $cp->progress,
                    'status' => $cp->status,
                    'started_at' => $cp->started_at,
                    'completed_at' => $cp->completed_at,
                ];
            }),
            'overall_progress' => $trackProgress->progress,
            'overall_status' => $trackProgress->status,
        ];
    }

    /**
     * Sync all course progress for a track
     */
    public function syncTrackProgress(User $student, Track $track): void
    {
        $trackProgress = TrackProgress::where('student_id', $student->id)
            ->where('track_id', $track->id)
            ->first();

        if (!$trackProgress) {
            return;
        }

        $courses = $track->courses;
        foreach ($courses as $course) {
            $courseProgress = TrackCourseProgress::where('track_progress_id', $trackProgress->id)
                ->where('course_id', $course->id)
                ->first();

            if ($courseProgress && $courseProgress->enrollment) {
                $courseProgress->updateFromEnrollment();
            }
        }

        $trackProgress->calculateProgress();
    }
}

