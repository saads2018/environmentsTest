<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class QuizResource extends JsonResource
{
    public static $wrap = 'quiz';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'article' => $this->article,
            'questions' => $this->questions,
            'codes' => $this->codes,
            'isCompleted' => $this->isQuizCompleted(),
            'completedBy' => $this->patientsCompleted->pluck('id'),
            'completedResults' => $this->patientsCompleted->pluck('pivot'),
            'created_at' => $this->created_at
        ];
    }


    private function isQuizCompleted() {
        $profileId = auth()->guard('api')->user()->profile->id;
        
        return $this->patientsCompleted->contains($profileId);
    }
}
