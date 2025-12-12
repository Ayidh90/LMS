<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [
        'course_id',
        'section_id',
        'type',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'content',
        'content_ar',
        'video_url',
        'live_meeting_date',
        'live_meeting_link',
        'order',
        'duration_minutes',
        'is_free',
    ];

    protected $appends = [
        'translated_title',
        'translated_description',
        'translated_content',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'duration_minutes' => 'integer',
            'is_free' => 'boolean',
            'live_meeting_date' => 'datetime',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // Translation helpers
    public function getTranslatedTitleAttribute()
    {
        return app()->getLocale() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getTranslatedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    public function getTranslatedContentAttribute()
    {
        return app()->getLocale() === 'ar' && $this->content_ar ? $this->content_ar : $this->content;
    }
}

