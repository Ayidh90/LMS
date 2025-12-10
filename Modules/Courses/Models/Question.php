<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'lesson_id',
        'type',
        'question',
        'question_ar',
        'explanation',
        'explanation_ar',
        'points',
        'order',
    ];

    protected $appends = [
        'translated_question',
        'translated_explanation',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function userAnswers()
    {
        return $this->hasMany(\App\Models\UserQuestionAnswer::class);
    }

    // Translation helpers
    public function getTranslatedQuestionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->question_ar ? $this->question_ar : $this->question;
    }

    public function getTranslatedExplanationAttribute()
    {
        return app()->getLocale() === 'ar' && $this->explanation_ar ? $this->explanation_ar : $this->explanation;
    }
}

