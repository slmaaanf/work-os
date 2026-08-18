<?php

namespace App\Http\Requests\Api;

use App\Enums\FocusSessionDecision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinishFocusSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::enum(FocusSessionDecision::class)],
            'result' => ['nullable', 'string', 'max:5000'],
        ];
    }
}