<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AppointmentCollection extends ResourceCollection
{
    public static $wrap = '';

    public function __construct($resource){
        $resource->loadMissing(['physician', 'patient']);
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'appointments' => $this->collection,
            'appointmentCount' => $this->count(),
            'meta' => [
                'count' => $this->total(),
                'currentPage'  => $this->currentPage(),
                'next'  => $this->nextPageUrl(),
                'last' => $this->lastPage()
            ],
        ];
    }
}
