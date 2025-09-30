<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhysicianMessageCollection extends ResourceCollection
{
    public static $wrap = '';

    public function __construct($resource){
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'messages' => $this->collection,
            'count' => $this->count()
        ];
    }
}
