<?php

namespace App\Http\Resources\Room;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\RoomType\RoomTypeResource;

class RoomDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => intval($this->id),
            'room_number' => strval($this->room_number),
            'room_type' => new RoomTypeResource($this->room_type),
            'price_per_month' => floatval($this->price_per_month),
            'status' => intval($this->status),
            'description' => strval($this->description),
        ];
    }
}
