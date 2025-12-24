<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackProgress extends Model
{
    use HasFactory;

    protected $table = 'track_progress';

    protected $fillable = [
        'student_id',
        'track_id',
        'progress',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'track_id' => 'integer',
            'progress' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function courseProgress()
    {
        return $this->hasMany(TrackCourseProgress::class);
    }

    /**
     * Calculate and update track progress based on course progress
     */
    public function calculateProgress(): void
    {
        $track = $this->track;
        if (!$track) {
            return;
        }

        $courses = $track->courses;
        if ($courses->isEmpty()) {
            $this->progress = 0;
            $this->save();
            return;
        }

        $totalCourses = $courses->count();
        $completedCourses = $this->courseProgress()
            ->where('status', 'completed')
            ->count();

        $totalProgress = $this->courseProgress()->sum('progress');
        $averageProgress = $totalProgress / ($totalCourses * 100) * 100;

        $this->progress = (int) round($averageProgress);

        // Update status
        if ($this->progress >= 100 && $completedCourses === $totalCourses) {
            $this->status = 'completed';
            if (!$this->completed_at) {
                $this->completed_at = now();
            }
        } else {
            $this->status = 'in_progress';
        }

        $this->save();
    }
}

