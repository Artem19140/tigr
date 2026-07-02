<?php

namespace App\Http\Resources\Exam;

use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamEditResource extends JsonResource
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
            'beginTime' => $this->begin_time_local->copy()->toIso8601String(),
            'capacity' => $this->capacity,
            'comment' => $this->comment,
            'examTypeId' =>  $this->type->id,
            'addressId' => $this->address->id,
            'examiners' => EmployeeResource::collection($this->whenLoaded('examiners')),
            'shortName' => $this->type->short_name
        ];
    }
}
