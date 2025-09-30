<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class LabResultResource extends JsonResource
{
    public static $wrap = 'lab_result';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'patient_id' => $this->patient->id,
            'patient' => $this->patient,
            'file' => $this->file,
            'date' => $this->date
        ];
    }
}
