<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizCompletedResource extends JsonResource
{
    public static $wrap = 'quiz';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'score' => $this->score,
            'completed_at' => $this->completed_at
        ];
    }
}