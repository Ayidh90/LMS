<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'batch_id',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
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
}
