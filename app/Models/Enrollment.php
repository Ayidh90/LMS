<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'batch_id',
        'course_id', // Keep for backward compatibility
        'status',
        'enrolled_at',
        'completed_at',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function course()
    {
        // Get course through batch
        if ($this->batch) {
            return $this->batch->course;
        }
        // Fallback for backward compatibility
        if ($this->course_id) {
            return \Modules\Courses\Models\Course::find($this->course_id);
        }
        return null;
    }
}

