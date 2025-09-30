<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

use App\Enums\AppointmentType;
use App\Enums\AppointmentVisitType;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patient_profiles,id',
            'start_time' => 'required',
            'finish_time' => 'required',
            // 'type' => ['required', new Enum(AppointmentType::class)],
            'visit_type' => ['required', new Enum(AppointmentVisitType::class)],
            'notes' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'patient_id.required' => 'Patient is required',
            'start_time.required' => 'Visit time is not set',
            'finish_time.required' => 'Visit time is not set',
            'visit_type.required' => 'Visit type is not set',
        ];
    }
}