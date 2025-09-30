<?php

namespace App\Http\Requests\PhysicianMessage;

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
            'limit' => 'sometimes|numeric',
            'sort' => ['sometimes', Rule::in(['is_read'])] //possibility to add more sorting params
        ];
    }
}