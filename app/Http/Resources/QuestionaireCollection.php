<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionaireCollection extends ResourceCollection
{
    public static $wrap = '';

    public function toArray($request): array
    {
        return [
            'questionnaires' => $this->collection,
            'meta' => [
                'count' => $this->total(),
                'currentPage'  => $this->currentPage(),
                'next'  => $this->nextPageUrl(),
                'last' => $this->lastPage()
            ],
        ];
    }
}
