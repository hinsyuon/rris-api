<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantDetailsResource extends JsonResource
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
            'first_name' => strval($this->first_name),
            'last_name' => strval($this->last_name),
            'gender' => intval($this->gender),
            'email' => strval($this->email),
            'phone_number' => strval($this->phone_number),
            'address' => strval($this->address),
            'joined_at' => strval($this->joined_at),
            
        ];
    }
}
