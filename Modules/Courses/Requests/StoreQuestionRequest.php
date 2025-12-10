<?php

namespace Modules\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'question' => ['required', 'string'],
            'question_ar' => ['nullable', 'string'],
            'explanation' => ['nullable', 'string'],
            'explanation_ar' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'answers' => ['required_if:type,multiple_choice,true_false', 'array', 'min:2'],
            'answers.*.answer' => ['required_with:answers', 'string'],
            'answers.*.answer_ar' => ['nullable', 'string'],
            'answers.*.is_correct' => ['required_with:answers', 'boolean'],
            'answers.*.order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => __('Please select a question type.'),
            'type.in' => __('Invalid question type selected.'),
            'question.required' => __('The question text is required.'),
            'points.required' => __('The points value is required.'),
            'points.integer' => __('Points must be a number.'),
            'points.min' => __('Points must be at least 1.'),
            'answers.required_if' => __('Please provide at least 2 answer options.'),
            'answers.min' => __('Please provide at least 2 answer options.'),
            'answers.*.answer.required_with' => __('Each answer option requires text.'),
            'answers.*.is_correct.required_with' => __('Please specify if each answer is correct.'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (in_array($this->type, ['multiple_choice', 'true_false']) && $this->has('answers')) {
                $hasCorrectAnswer = collect($this->answers)->contains('is_correct', true);
                if (!$hasCorrectAnswer) {
                    $validator->errors()->add('answers', __('At least one answer must be marked as correct.'));
                }
            }
        });
    }
}

