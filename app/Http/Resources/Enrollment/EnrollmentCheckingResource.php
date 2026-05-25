<?php

namespace App\Http\Resources\Enrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentCheckingResource extends JsonResource
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
            'regNum' => $this->reg_number,
            'attempt' => $this->whenLoaded('attempt', fn () => [
                'id' => $this->attempt->id,
                'checked_at' => $this->attempt->checked_at,
                'isPassed' => $this->attempt->is_passed,
            ]),
        ];
    }
}
