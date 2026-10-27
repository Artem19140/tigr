<?php

namespace App\Http\Resources\ForeignNational;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'fullName' => $this->resource->surname . ' ' . $this->resource->name,
            'showUrl' => route('foreign-nationals.show', [
                'foreign_national' => $this->resource
            ], false),
            'dateBirth' => $this->resource->formattedDateBirth(),
        ];
    }
}
