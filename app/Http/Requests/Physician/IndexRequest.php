<?php

namespace App\Http\Requests\Physician;

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
            'profile.dob' => 'sometimes|date',
            'profile.gender' => ['sometimes', Rule::in(['m', 'f'])]
        ];
    }
}