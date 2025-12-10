<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'content',
        'video_url',
        'order',
        'duration_minutes',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'duration_minutes' => 'integer',
            'is_free' => 'boolean',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

