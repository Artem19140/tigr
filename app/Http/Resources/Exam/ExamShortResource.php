<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamShortResource extends JsonResource
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
            'examTypeId' => $this->whenLoaded('type', fn () => $this->type->id),
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'name' => $this->whenLoaded('type', fn () => $this->type->name),
            'beginTime' => $this->begin_time_local->toIso8601String(),
            'cancelledAt' => $this->cancelled_at
        ];
    }
}
