<?php

namespace App\Http\Requests\ClinicalNote;

use Illuminate\Foundation\Http\FormRequest;

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
            'appt_id' => 'required|exists:appointments,id',
            'quiz_id' => 'sometimes|exists:quizzes,id',
            'health_data_id' => 'sometimes|exists:health_data,id',
            'time_in' => 'sometimes|string|nullable',
            'time_out' => 'sometimes|string|nullable',
            'counselling' => 'sometimes|string|nullable',
            'discussed' => 'sometimes|string|nullable',
            'homework' => 'sometimes|string|nullable',
            'next_followup_physical' => 'sometimes|string|nullable',
            'next_followup_labs' => 'sometimes|string|nullable',

            'health-data.height' => 'sometimes|string|max:255|nullable',
            'health-data.weight' => 'sometimes|string|max:255|nullable',
            'health-data.bmi' => 'sometimes|numeric|max:255|nullable',
            'health-data.bodyfat' => 'sometimes|string|max:255|nullable',
            'health-data.bp' => 'sometimes|string|max:255|nullable',
            'health-data.resting_hr' => 'sometimes|string|max:255|nullable',

            'include' => 'sometimes|boolean|max:255|nullable',
            'age' => 'sometimes|boolean|max:255|nullable',
            'height' => 'sometimes|boolean|max:255|nullable',
            'weight' => 'sometimes|boolean|max:255|nullable',
            'bmi' => 'sometimes|boolean|max:255|nullable',
            'ibw' => 'sometimes|string|max:255|nullable',
            'bmr' => 'sometimes|string|max:255|nullable',
            'food_allergies' => 'sometimes|string|max:255|nullable',
            'med_allergies' => 'sometimes|string|max:255|nullable',
            'nutrition_rel_labs' => 'sometimes|string|max:255|nullable',
            'nutrition_rel_meds' => 'sometimes|string|max:255|nullable',
            'nutrition_rel_diag' => 'sometimes|string|max:255|nullable',
            'diet_order' => 'sometimes|string|max:255|nullable',
            'texture' => 'sometimes|string|max:255|nullable',
            'complications' => 'sometimes|string|max:255|nullable',
            'est_cal_per_day' => 'sometimes|string|max:255|nullable',
            'est_protein_per_day' => 'sometimes|string|max:255|nullable',
            'est_carbs_per_day' => 'sometimes|string|max:255|nullable',
            'est_fat_per_day' => 'sometimes|string|max:255|nullable',
            'est_fluid_per_day' => 'sometimes|string|max:255|nullable',
            'intake' => 'sometimes|string|max:255|nullable',
            'activity' => 'sometimes|string|max:255|nullable',
            'interventions' => 'sometimes|string|max:255|nullable',
            'plan' => 'sometimes|string|max:255|nullable',
            'notes' => 'sometimes|string|max:255|nullable',

            'icd_code' => 'sometimes|string|max:255|nullable',
            'cpt_code' => 'sometimes|string|max:255|nullable',
        ];
    }
}