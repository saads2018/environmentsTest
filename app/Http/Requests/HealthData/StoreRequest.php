<?php

namespace App\Http\Requests\HealthData;

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
            'height' => 'sometimes|string|max:255',
            'weight' => 'sometimes|string|max:255',
            'bmi' => 'sometimes|numeric|max:255',
            'bodyfat' => 'sometimes|string|max:255',
            'bp' => 'sometimes|string|max:255',
            'resting_hr' => 'sometimes|string|max:255',
        ];
    }
}