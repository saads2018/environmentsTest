<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'servings' => 'sometimes|integer',
            'cook_time' => 'sometimes|integer',
            'image' => 'sometimes|string|max:255|nullable',
            'attachment' => 'sometimes|string|max:255|nullable',
            'ingredients' => 'sometimes|array|min:1',
            'steps' => 'sometimes|array|min:1',
            'codes' => 'sometimes|array'
        ];
    }
}