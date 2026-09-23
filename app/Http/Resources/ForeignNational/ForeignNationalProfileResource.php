<?php

namespace App\Http\Resources\ForeignNational;

use App\Http\Resources\Enrollment\EnrollmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalProfileResource extends JsonResource
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
            'dateBirth' => $this->resource->date_birth,
            'issuedBy' => $this->resource->issued_by,
            'issuedDate' => $this->issued_date,
            'citizenship' => $this->resource->citizenship,
            'phone' => $this->resource->phone ?? null,
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'fullName' => $this->full_name,
            'fullNameLatin' => $this->full_name_latin,
            'fullPassport' => $this->full_passport,
            'creatorFullName' => $this->whenLoaded('creator', fn () => $this->creator->full_name),
            'documents' =>  ForeignNationalDocumentResource::collection($this->whenLoaded('documents'))
        ];
    }
}
