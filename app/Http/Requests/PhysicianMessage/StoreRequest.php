<?php

namespace App\Http\Requests\PhysicianMessage;

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
            'conversation_id' => 'sometimes|nullable',
            'to_id' => 'sometimes|nullable',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}