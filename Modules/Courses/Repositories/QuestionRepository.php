<?php

namespace Modules\Courses\Repositories;

use Modules\Courses\Models\Question;
use Modules\Courses\Models\Answer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QuestionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Question());
    }

    public function getByLesson(int $lessonId, array $relations = ['answers']): Collection
    {
        return Question::where('lesson_id', $lessonId)
            ->with($relations)
            ->orderBy('order')
            ->get();
    }

    public function getNextOrder(int $lessonId): int
    {
        return (Question::where('lesson_id', $lessonId)->max('order') ?? 0) + 1;
    }

    public function createWithAnswers(array $questionData, array $answers): Question
    {
        return DB::transaction(function () use ($questionData, $answers) {
            $question = Question::create($questionData);
            
            foreach ($answers as $answerData) {
                $question->answers()->create($answerData);
            }
            
            return $question->load('answers');
        });
    }

    public function updateWithAnswers(Question $question, array $questionData, array $answers): Question
    {
        return DB::transaction(function () use ($question, $questionData, $answers) {
            $question->update($questionData);
            
            // Delete existing answers and recreate
            $question->answers()->delete();
            
            foreach ($answers as $answerData) {
                $question->answers()->create($answerData);
            }
            
            return $question->fresh(['answers']);
        });
    }

    public function reorder(int $lessonId, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            Question::where('id', $id)
                ->where('lesson_id', $lessonId)
                ->update(['order' => $order + 1]);
        }
    }

    public function countByLesson(int $lessonId): int
    {
        return Question::where('lesson_id', $lessonId)->count();
    }

    public function getTotalPoints(int $lessonId): int
    {
        return Question::where('lesson_id', $lessonId)->sum('points') ?? 0;
    }
}
