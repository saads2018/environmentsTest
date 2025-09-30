<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClinicalNoteCollection extends ResourceCollection
{
    public static $wrap = '';

    public function __construct($resource){
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'clinical_notes' => $this->collection,
            'count' => $this->count()
        ];
    }
}
