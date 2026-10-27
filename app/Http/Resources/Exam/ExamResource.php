<?php

namespace App\Http\Resources\Exam;

use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Enrollment\EnrollmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cancelledReason' => $this->when($this->isCancelled(), $this->cancelled_reason),
            'cancelledAt' => $this->resource->cancelled_at,
            'beginTime' => $this->resource->beginTimeLocal()->toIso8601String(),
            'endTime' => $this->resource->endTimeLocal()->toIso8601String(),
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'sessionNumber' => $this->session,
            'capacity' => $this->capacity,
            'comment' => $this->comment,
            'group' => $this->group,
            'examiners' =>$this->whenLoaded('examiners', $this->examiners->implode('full_name',  ', ')),
            'name' => $this->whenLoaded('type', fn () => $this->fullName()),
            'shortName' => $this->whenLoaded('type', fn () => $this->shortName()),
            'address' => $this->whenLoaded('address', fn () => $this->addressName()),
        ];
    }
}
