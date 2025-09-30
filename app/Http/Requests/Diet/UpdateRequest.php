<?php

namespace App\Http\Requests\Diet;

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
            'description' => 'sometimes|string',
            'image' => 'sometimes|string|max:255|nullable',
            'attachment' => 'sometimes|string|max:255|nullable',
            'days' => 'sometimes|array|min:1',
            'codes' => 'sometimes|array'
        ];
    }
}