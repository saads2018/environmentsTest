<?php

namespace App\Http\Requests\Diet;

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
            'description' => 'required|string',
            'image' => 'sometimes|string|max:255|nullable',
            'attachment' => 'sometimes|string|max:255|nullable',
            'days' => 'required|array|min:1',
            'codes' => 'required|array'
        ];
    }
}