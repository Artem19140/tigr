<?php

namespace App\Http\Resources\Enrollment;

use App\Http\Resources\Attempt\AttemptMonitoringResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentMonitoringResource extends JsonResource
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
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'hasPayment' => $this->has_payment,
            'attempt' => new AttemptMonitoringResource($this->whenLoaded('attempt')),
            'availability' => [
                'payment' => $this->payment_available
            ],
        ];
    }
}
