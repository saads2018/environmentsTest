<?php

namespace App\Http\Requests\LabResult;

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
            'name' => 'required|string|max:255',
            'patient_id' => 'required|string',
            'file' => 'required|array|max:255',
            'date' => 'required|date',
        ];
    }
}