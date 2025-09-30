<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class AppointmentResource extends JsonResource
{
    public static $wrap = 'appointment';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'physician' => new PhysicianResource($this->whenLoaded("physician")),
            'patient' => new ProfileResource($this->whenLoaded("patient")),
            'start_time' => $this->start_time,
            'finish_time' => $this->finish_time,
            'duration' => $this->finish_time->diffInMinutes($this->start_time),
            'type' => $this->type,
            'visit_type' => $this->visit_type,
            'visit_type_readable' => $this->visit_type->label(),
            'notes' => $this->notes,
            'clinical_note_id' => $this->note?->id,
        ];
    }
}
