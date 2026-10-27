<?php

namespace App\Http\Resources\ForeignNational;

use App\Http\Resources\Enrollment\EnrollmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalResource extends JsonResource
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
            'dateBirth' => $this->resource->formattedDateBirth(),
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
        ];
    }
}
