<?php

namespace App\Http\Resources\Exam;

use App\Modules\Exam\ProtocolCommentRules;
use App\Http\Resources\Enrollment\EnrollmentConductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamConductResource extends JsonResource
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
            'enrollments' => EnrollmentConductResource::collection($this->whenLoaded('enrollments')),
            'enrollmentsCount' => $this->whenCounted('enrollments_count'),
            'protocolComment' => $this->protocol_comment,
            'hasSpeakingTasks' => $this->whenLoaded('type', fn () => $this->type->has_speaking_tasks),
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'polling' => $this->isGoing(),
            'actions' => [
                'protocolComment' => [
                    'available' =>  app(ProtocolCommentRules::class)->check($this->resource)->available
                ]
            ]
        ];
    }
}
