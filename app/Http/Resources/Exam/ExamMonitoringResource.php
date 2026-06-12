<?php

namespace App\Http\Resources\Exam;

use App\Domain\Exam\Resolver\ExamStatusResolver;
use App\Http\Resources\Enrollment\EnrollmentMonitoringResource;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamMonitoringResource extends JsonResource
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
            'enrollments' => EnrollmentMonitoringResource::collection($this->whenLoaded('enrollments')),
            'endTime' => $this->begin_time_local->copy()->addMinutes($this->duration)->toIso8601String(),
            'enrollmentsCount' => $this->whenCounted('enrollments_count'),
            'protocolComment' => $this->protocol_comment,
            'hasSpeakingTasks' => $this->whenLoaded('type', fn () => $this->type->has_speaking_tasks),
            'status' => app(ExamStatusResolver::class)->execute($this->resource),
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'polling' => $this->pollingStart($this->resource) 
        ];
    }

    protected function pollingStart(Exam $exam){
        
        return $exam->isGoing() && $exam->enrollments_count > 0 ;
    }
}
