<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;


class MassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [ 
            'text' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'text.required' => 'Quote content is required',
        ];
    }
}