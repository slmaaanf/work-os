<?php

namespace App\Http\Requests\Api;

use App\Enums\ActivityCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(ActivityCategory::class)],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }
}