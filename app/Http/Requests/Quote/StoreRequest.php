<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;


class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [ 
            'text' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Quote content is required',
        ];
    }
}