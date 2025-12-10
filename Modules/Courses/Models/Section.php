<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

    protected $fillable = [
        'course_id',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'order',
    ];

    protected $appends = [
        'translated_title',
        'translated_description',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
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
}

