<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
        'answer_text',
        'is_correct',
        'points_earned',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'points_earned' => 'integer',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(\Modules\Courses\Models\Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(\Modules\Courses\Models\Answer::class);
    }
}
