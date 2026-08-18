<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CarryOverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'target_date' => ['required', 'date'],
            'planned_mins' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ];
    }
}