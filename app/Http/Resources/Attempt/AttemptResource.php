<?php

namespace App\Http\Resources\Attempt;

use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Http\Resources\TaskVariant\TaskVariantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptResource extends JsonResource
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
            'examName' => $this->whenLoaded('exam', fn () => $this->exam->type->short_name),
            'answers' => AttemptAnswerResource::collection($this->whenLoaded('answers')),
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'startedAt' => $this->started_at_local?->toIso8601String(),
            'finishedAt' => $this->finished_at_local?->toIso8601String(),
            'isPassed' => $this->is_passed,
            'tasks' => TaskVariantResource::collection($this->whenLoaded('taskVariants', fn () => $this->taskVariants)),
            'checkedAt' => $this->checked_at,
            'speakingFinishedAt' => $this->resource->speaking_finished_at,
            'speakingStartedAt' => $this->resource->speaking_started_at,
        ];
    }
}
