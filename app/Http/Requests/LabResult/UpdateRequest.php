<?php

namespace App\Http\Requests\LabResult;

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
            'name' => 'sometimes|string|max:255',
            'patient_id' => 'sometimes|string',
            'file' => 'sometimes|array|max:255',
            'date' => 'sometimes|date',
        ];
    }
}