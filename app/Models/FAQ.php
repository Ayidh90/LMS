<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'question_ar',
        'answer',
        'answer_ar',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Translation helpers
    public function getTranslatedQuestionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->question_ar ? $this->question_ar : $this->question;
    }

    public function getTranslatedAnswerAttribute()
    {
        return app()->getLocale() === 'ar' && $this->answer_ar ? $this->answer_ar : $this->answer;
    }
}
