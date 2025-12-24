<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackCourseProgress extends Model
{
    use HasFactory;

    protected $table = 'track_course_progress';

    protected $fillable = [
        'track_progress_id',
        'course_id',
        'enrollment_id',
        'progress',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'track_progress_id' => 'integer',
            'course_id' => 'integer',
            'enrollment_id' => 'integer',
            'progress' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Relationships
    public function trackProgress()
    {
        return $this->belongsTo(TrackProgress::class);
    }

    public function course()
    {
        return $this->belongsTo(\Modules\Courses\Models\Course::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Update progress from enrollment
     */
    public function updateFromEnrollment(): void
    {
        if (!$this->enrollment) {
            return;
        }

        $this->progress = $this->enrollment->progress ?? 0;

        if ($this->progress >= 100 || $this->enrollment->status === 'completed') {
            $this->status = 'completed';
            if (!$this->completed_at) {
                $this->completed_at = now();
            }
        } elseif ($this->progress > 0) {
            $this->status = 'in_progress';
            if (!$this->started_at) {
                $this->started_at = now();
            }
        } else {
            $this->status = 'not_started';
        }

        $this->save();

        // Update parent track progress
        if ($this->trackProgress) {
            $this->trackProgress->calculateProgress();
        }
    }
}

