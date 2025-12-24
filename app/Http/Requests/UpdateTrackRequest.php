<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'program_id.required' => __('The program is required.'),
            'program_id.exists' => __('The selected program does not exist.'),
            'name.required' => __('The track name is required.'),
            'thumbnail.image' => __('The thumbnail must be an image.'),
            'thumbnail.max' => __('The thumbnail must not exceed 2MB.'),
        ];
    }
}

