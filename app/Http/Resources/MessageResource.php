<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class MessageResource extends JsonResource
{
    public static $wrap = 'message';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'from_id' => $this->from_id,
            'from' => $this->whenLoaded("from"),
            'patient_id' => $this->patient_id,
            'patient' => new ProfileResource($this->whenLoaded("patient")),
            'is_read' => $this->isRead,

            'body' => $this->body,
            'created_at' => $this->created_at
        ];
    }
}
