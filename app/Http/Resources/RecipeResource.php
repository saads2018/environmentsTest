<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;

class RecipeResource extends JsonResource
{
    public static $wrap = 'recipe';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'servings' => $this->servings,
            'cook_time' => $this->cook_time,
            'image' => $this->image,
            'attachment' => $this->attachment,
            'ingredients' => $this->ingredients,
            'steps' => $this->steps,
            'codes' => $this->codes,
            'created_at' => $this->created_at
        ];
    }
}
