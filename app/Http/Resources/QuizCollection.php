<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizCollection extends ResourceCollection
{
    public static $wrap = '';

    public function toArray($request): array
    {
        return [
            'quizzes' => $this->collection,
            'meta' => [
                'count' => $this->total(),
                'currentPage'  => $this->currentPage(),
                'next'  => $this->nextPageUrl(),
                'last' => $this->lastPage()
            ],
        ];
    }
}
