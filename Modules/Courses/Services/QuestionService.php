<?php

namespace Modules\Courses\Services;

use Modules\Courses\Repositories\QuestionRepository;
use Modules\Courses\Models\Question;
use Modules\Courses\Models\Lesson;
use App\Models\UserQuestionAnswer;
use Illuminate\Database\Eloquent\Collection;

class QuestionService
{
    public function __construct(
        private QuestionRepository $repository
    ) {}

    public function getByLesson(int $lessonId, array $relations = ['answers']): Collection
    {
        return $this->repository->getByLesson($lessonId, $relations);
    }

    public function getById(int $id, array $relations = ['answers']): ?Question
    {
        return $this->repository->findById($id, $relations);
    }

    public function createWithAnswers(array $questionData, array $answers): Question
    {
        $questionData['order'] = $questionData['order'] ?? $this->repository->getNextOrder($questionData['lesson_id']);
        
        return $this->repository->createWithAnswers($questionData, $answers);
    }

    public function updateWithAnswers(Question $question, array $questionData, array $answers): Question
    {
        return $this->repository->updateWithAnswers($question, $questionData, $answers);
    }

    public function delete(Question $question): bool
    {
        return $this->repository->delete($question);
    }

    public function reorder(int $lessonId, array $orderedIds): void
    {
        $this->repository->reorder($lessonId, $orderedIds);
    }

    public function countByLesson(int $lessonId): int
    {
        return $this->repository->countByLesson($lessonId);
    }

    public function getTotalPoints(int $lessonId): int
    {
        return $this->repository->getTotalPoints($lessonId);
    }

    public function belongsToLesson(Question $question, Lesson $lesson): bool
    {
        return $question->lesson_id === $lesson->id;
    }

    public function formatForFrontend(Question $question, $userAnswers = null): array
    {
        $userAnswer = $userAnswers ? $userAnswers->get($question->id) : null;
        
        return [
            'id' => $question->id,
            'type' => $question->type,
            'question' => $question->translated_question ?? $question->question,
            'question_ar' => $question->question_ar,
            'explanation' => $question->translated_explanation ?? $question->explanation,
            'explanation_ar' => $question->explanation_ar,
            'points' => $question->points,
            'order' => $question->order,
            'answers' => $question->answers->map(fn($a) => [
                'id' => $a->id,
                'answer' => $a->translated_answer ?? $a->answer,
                'answer_ar' => $a->answer_ar,
                'is_correct' => $a->is_correct,
                'order' => $a->order,
            ]),
            'user_answer' => $userAnswer ? [
                'id' => $userAnswer->id,
                'answer_id' => $userAnswer->answer_id,
                'answer_text' => $userAnswer->answer_text,
                'is_correct' => $userAnswer->is_correct,
                'points_earned' => $userAnswer->points_earned,
            ] : null,
        ];
    }

    /**
     * Get user answers grouped by question
     */
    public function getUserAnswersGrouped(Lesson $lesson): \Illuminate\Support\Collection
    {
        return UserQuestionAnswer::whereIn('question_id', $lesson->questions->pluck('id'))
            ->with(['user:id,name,email', 'answer:id,answer,answer_ar'])
            ->get()
            ->groupBy('question_id')
            ->map(fn($answers) => $answers->map(fn($a) => [
                'id' => $a->id,
                'user' => ['id' => $a->user->id, 'name' => $a->user->name],
                'answer_id' => $a->answer_id,
                'answer_text' => $a->answer_text,
                'is_correct' => $a->is_correct,
                'points_earned' => $a->points_earned,
            ]));
    }
}
