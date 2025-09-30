<?php

namespace App\Http\Requests\Physician;

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
            'user.first_name' => 'sometimes|string|max:255',
            'user.last_name' => 'sometimes|string|max:255',
            'user.image' => 'sometimes|nullable|string|max:255',
            'user.phone' => 'sometimes|string|max:255',
            
            'profile.dob' => 'sometimes|date',
            'profile.gender' => ['sometimes', Rule::in(['m', 'f'])],
        ];
    }
}