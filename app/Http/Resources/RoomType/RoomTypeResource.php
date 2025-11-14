<?php

namespace App\Http\Resources\RoomType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            'id' => intval($this->id),
            'name' => strval($this->name),
            'description' => strval($this->description),
        ];
    }
}
