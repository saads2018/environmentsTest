<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProfileResource extends JsonResource
{
    public static $wrap = 'patient';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'ethnicity' => $this->ethnicity,
            'race' => $this->race,

            'patient_confirmed' => $this->patient_confirmed,
            'language' => $this->language,
            'religion' => $this->religion,
            'notes' => $this->notes,

            'insurance_info' => is_array($this->insurance_info) ? $this->insurance_info : null,
            'emergency_contact' => is_array($this->emergency_contact) ? $this->emergency_contact : null ,
            'contact_info' => is_array($this->contact_info) ? $this->contact_info : null,

            'meds' => is_array($this->meds) ? array_filter($this->meds) : null ,
            'dxcode' => $this->dxcode,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'additional_data' => [
                'weight' => @$this->getData()['weight'] ?: '',
                'height' => @$this->getData()['height'] ?: '',
                'bmi' => @$this->getData()['bmi'] ?: '',
                'allergies' => @$this->getData()['allergies'] ?: '',

                // 'sleep_hours' => $this->sleep_hours,
                // 'activity_level' => $this->activity_level,
                // 'stress_levels' => $this->stress_levels,
                // 'waist_size' => $this->waist_size,
                // 'alcohol_consumption' => $this->alcohol_consumption,
                // 'caffeine_consumption' => $this->caffeine_consumption,
                // 'eat_out_level' => $this->eat_out_level,
            ],
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'image' => $this->user->image,
            ],
            'family-history' => FamilyHistoryResource::collection($this->whenLoaded("familyHistory")),
            'conditions' => ConditionResouce::collection($this->whenLoaded("conditions")),
            'health_data' => HealthDataResource::collection($this->whenLoaded("healthData")),
            'current_health_data' => new HealthDataResource($this->currentHealthData->first()),
            'appointments' => AppointmentResource::collection($this->whenLoaded("appointments")),
            'questionnaire' => $this->questionaires->first(),
            'questionnaireAssignDate' => $this->quizAssignDate,
            'questionnaireRequired' => $this->questionnaireRequired,
            'completed_quizzes' => QuizCompletedResource::collection($this->completedQuizzes2()),
            'educationCodes' => $this->codes,
            'clinicalNotes' => ClinicalNoteResource::collection($this->whenLoaded('clinicalNotes'))
        ];
    }

    private function getData(): array {
        return is_array($this->data) ? $this->data : [];
    }

}