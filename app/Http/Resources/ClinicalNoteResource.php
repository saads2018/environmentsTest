<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class ClinicalNoteResource extends JsonResource
{
    public static $wrap = 'clinical_note';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'appt_id' => $this->appt_id,
            'health_data_id' => $this->health_data_id,

            'time_in' => $this->time_in,
            'time_out' => $this->time_out,
            'counselling' => $this->counselling,
            'discussed' => $this->discussed,
            'next_appt' => $this->next_appt,
            'homework' => $this->homework,
            'next_followup_physical' => $this->next_followup_physical,
            'next_followup_labs' => $this->next_followup_labs,

            'patient' => new ProfileResource($this->whenLoaded('patient')),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'health_data' => new HealthDataResource($this->whenLoaded('healthData')),

            'include' => $this->include,
            'age' => $this->age,
            'weight' => $this->weight,
            'height' => $this->height,
            'bmi' => $this->bmi,
            'ibw' => $this->ibw,
            'bmr' => $this->bmr,
            'food_allergies' => $this->food_allergies,
            'med_allergies' => $this->med_allergies,
            'nutrition_rel_labs' => $this->nutrition_rel_labs,
            'nutrition_rel_meds' => $this->nutrition_rel_meds,
            'nutrition_rel_diag' => $this->nutrition_rel_diag,
            'diet_order' => $this->diet_order,
            'texture' => $this->texture,
            'complications' => $this->complications,
            'est_cal_per_day' => $this->est_cal_per_day,
            'est_protein_per_day' => $this->est_protein_per_day,
            'est_carbs_per_day' => $this->est_carbs_per_day,
            'est_fat_per_day' => $this->est_fat_per_day,
            'est_fluid_per_day' => $this->est_fluid_per_day,
            'intake' => $this->intake,
            'activity' => $this->activity,
            'interventions' => $this->interventions,
            'plan' => $this->plan,
            'notes' => $this->notes,

            'icd_code' => $this->icd_code,
            'cpt_code' => $this->cpt_code,

            'soapId' => $this->soap?->id,
        ];
    }
}
