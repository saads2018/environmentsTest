<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'servings' => 'required|integer',
            'cook_time' => 'required|integer',
            'image' => 'sometimes|string|max:255|nullable',
            'attachment' => 'sometimes|string|max:255|nullable',
            'ingredients' => 'required|array|min:1',
            'steps' => 'required|array|min:1',
            'codes' => 'required|array'
        ];
    }
}