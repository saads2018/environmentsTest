<?php

namespace App\Http\Requests\CorporateQuiz;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:255',
        ];
    }
}