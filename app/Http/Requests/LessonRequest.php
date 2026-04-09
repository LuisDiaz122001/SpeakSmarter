<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match ($this->route()?->getActionMethod()) {
            'store' => $this->user()?->can('create lessons') ?? false,
            'update' => $this->user()?->can('update lessons') ?? false,
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'image_uri' => ['nullable', 'string', 'max:255'],
            'content_uri' => ['nullable', 'string', 'max:255'],
            'pdf_uri' => ['nullable', 'string', 'max:255'],
            'is_free' => ['required', 'boolean'],
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => ! $this->boolean('is_free')),
            ],
            'level_id' => ['nullable', Rule::exists('levels', 'id')],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [Rule::exists('categories', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The lesson name is required.',
            'description.required' => 'The lesson description is required.',
            'price.required' => 'A price is required when the lesson is not free.',
            'price.numeric' => 'The lesson price must be a valid number.',
            'price.min' => 'The lesson price cannot be negative.',
            'level_id.exists' => 'The selected level is invalid.',
            'category_ids.array' => 'The categories selection is invalid.',
            'category_ids.*.exists' => 'One of the selected categories is invalid.',
        ];
    }
}
