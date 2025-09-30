<?php

namespace App\Http\Requests\ClinicalNote;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|numeric',
            'searchTerm' => 'sometimes|nullable|string|max:255'
        ];
    }
}