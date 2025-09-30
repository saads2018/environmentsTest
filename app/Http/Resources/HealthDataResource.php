<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HealthDataResource extends JsonResource
{
    public static $wrap = 'health_data';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'height' => $this->height,
            'weight' => $this->weight,
            'bmi' => $this->bmi,
            'bodyfat' => $this->bodyfat,
            'bp' => $this->bp,
            'resting_hr' => $this->resting_hr,
            'created_at' => $this->created_at
        ];
    }

}