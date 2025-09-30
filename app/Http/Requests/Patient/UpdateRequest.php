<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

use App\Enums\EducationCode;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user.first_name' => 'sometimes|string|max:255',
            'user.last_name' => 'sometimes|string|max:255',
            'user.image' => 'sometimes|nullable|string|max:255',
            'user.phone' => 'sometimes|string|max:255',

            'profile.dob' => 'sometimes|date|nullable',
            'profile.gender' => ['sometimes', Rule::in(['m', 'f']), 'nullable'],
            'profile.ethnicity' => 'sometimes|string|max:255|nullable',
            'profile.race' => 'sometimes|string|max:255|nullable',
            'profile.language' => 'sometimes|string|max:255|nullable',
            'profile.religion' => 'sometimes|string|max:255|nullable',
            'profile.notes' => 'sometimes|string|nullable',
            'profile.physicians' => 'sometimes|integer|nullable',

            'insurance.name' => 'sometimes|string|max:255|nullable',
            'insurance.number' => 'sometimes|string|max:255|nullable',

            'emergency.contact' => 'sometimes|string|max:255|nullable',
            'emergency.phone' => 'sometimes|string|max:255|nullable',
            'emergency.relation' => 'sometimes|string|max:255|nullable',

            'contact.address' => 'sometimes|string|max:255|nullable',
            'contact.current-address' => 'sometimes|string|max:255|nullable',
            'contact.city' => 'sometimes|string|max:255|nullable',
            'contact.zip' => 'sometimes|string|max:255|nullable',
            'contact.address' => 'sometimes|string|max:255|nullable',

            'health-data.weight' => 'sometimes|string|max:255|nullable',
            'health-data.height' => 'sometimes|string|max:255|nullable',
            'health-data.bmi' => 'sometimes|numeric|nullable',

            'additional-data.allergies' => 'sometimes|string|max:255|nullable',
            'additional-data.stress_levels' => 'sometimes|integer|nullable',
            'additional-data.waist_size' => 'sometimes|integer|nullable',
            'additional-data.alcohol_consumption' => 'sometimes|integer|nullable',
            'additional-data.caffeine_consumption' => 'sometimes|integer|nullable',
            'additional-data.eat_out_level' => 'sometimes|integer|nullable',

            'meds' => 'sometimes|array|nullable',

            'dxcode' => ['sometimes', new Enum(EducationCode::class), 'nullable'],
        ];
    }

    public function messages()
    {
        return [
            'user.phone' => "Mobile phone is required."
        ];
    }
}