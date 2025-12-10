<?php

namespace Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answers';

    protected $fillable = [
        'question_id',
        'answer',
        'answer_ar',
        'is_correct',
        'order',
    ];

    protected $appends = [
        'translated_answer',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Translation helpers
    public function getTranslatedAnswerAttribute()
    {
        return app()->getLocale() === 'ar' && $this->answer_ar ? $this->answer_ar : $this->answer;
    }
}

