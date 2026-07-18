<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'address' => $this->address,
            'id' => $this->id,
            'capacity' => $this->capacity,
            'examsExists' => $this->examsExists ?? null,
            'isActive' => $this->is_active,
        ];
    }
}
