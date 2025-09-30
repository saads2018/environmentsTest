<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sections' => 'required|array',
            'sections.cardio' => 'required|array',
            'sections.glucose' => 'required|array',
            'sections.endo' => 'required|array',
            'sections.gi' => 'required|array',
            "lifestyle_data" => 'required|array',

        ];
    }
}