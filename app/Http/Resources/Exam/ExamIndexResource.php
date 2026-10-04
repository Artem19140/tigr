<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamIndexResource extends JsonResource
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
            'beginTime' => $this->resource->beginTimeLocal()->toIso8601String(),
            'capacity' => $this->capacity,
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'enrollmentsCount' => $this->whenCounted('enrollments_count'),
            'showUrl' => route('exams.show', [
                'exam' => $this->resource
            ], false)
        ];
    }
}
