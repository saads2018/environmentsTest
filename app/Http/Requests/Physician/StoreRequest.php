<?php

namespace App\Http\Requests\Physician;

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
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.email' => 'required|email|max:255|unique:users,email',
            'profile.dob' => 'required|date',
            'profile.gender' => ['required', Rule::in(['m', 'f'])],
        ];
    }
}