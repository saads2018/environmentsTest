<?php

namespace App\Http\Requests\Message;

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
            'body' => 'sometimes|nullable',
            'patient_id' => 'required|exists:patient_profiles,id',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}