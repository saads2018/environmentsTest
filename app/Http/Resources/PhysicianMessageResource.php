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
            'from_id' => $this->from_id,
            'from' => $this->whenLoaded("from"),
            'is_read' => $this->isRead,
            'conversation_id' => $this->conversation_id,
            'conversation' => $this->whenLoaded('conversation'),
            'body' => $this->body,
            'created_at' => $this->created_at
        ];
    }
}
