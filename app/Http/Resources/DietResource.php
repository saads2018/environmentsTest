<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class DietResource extends JsonResource
{
    public static $wrap = 'diet';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'attachment' => $this->attachment,
            'duration' => count($this->data['days']),
            'days' => $this->data['days'],
            'codes' => $this->codes,
        ];
    }
}
