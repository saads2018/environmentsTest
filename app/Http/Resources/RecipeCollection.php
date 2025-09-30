<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecipeCollection extends ResourceCollection
{
    public static $wrap = '';

    public function __construct($resource){
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'recipes' => $this->collection,
            'meta' => [
                'count' => $this->total(),
                'currentPage'  => $this->currentPage(),
                'next'  => $this->nextPageUrl(),
                'last' => $this->lastPage()
            ],
            
        ];
    }
}
