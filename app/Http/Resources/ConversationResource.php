<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class PhysicianMessageResource extends JsonResource
{
    public static $wrap = 'p_message';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_message' => $this->last_message,
            'messages' => $this->whenLoaded('messages'),
            'participants' => $this->participants,
            'other_participant' => $this->other_participant,
            'created_at' => $this->created_at,
        ];
    }
}
