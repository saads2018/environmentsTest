<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class QuoteResource extends JsonResource
{
    public static $wrap = 'quote';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            'scheduled_at' => $this->scheduled_at
        ];
    }
}
