<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhysicianResource extends JsonResource
{
    public static $wrap = 'physician';

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'image' => $this->user->image,
            ],
        ];
    }
}
