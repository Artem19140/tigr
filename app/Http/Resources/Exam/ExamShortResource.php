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
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'beginTime' => $this->begin_time_local->toIso8601String(),
            'cancelledAt' => $this->cancelled_at
        ];
    }
}
