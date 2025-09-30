<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhysicianCollection extends ResourceCollection
{
    public static $wrap = '';

    public function toArray($request): array
    {
        return [
            'physicians' => $this->collection,
            'physiciansCount' => $this->count()
        ];
    }
}
