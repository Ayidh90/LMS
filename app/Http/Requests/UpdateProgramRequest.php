<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
            'name.required' => __('The program name is required.'),
            'thumbnail.image' => __('The thumbnail must be an image.'),
            'thumbnail.max' => __('The thumbnail must not exceed 2MB.'),
        ];
    }
}

