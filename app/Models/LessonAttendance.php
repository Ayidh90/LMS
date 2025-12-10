<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'batch_id',
        'marked_by',
        'status',
        'notes',
        'attended_at',
    ];

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
        ];
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(\Modules\Courses\Models\Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
