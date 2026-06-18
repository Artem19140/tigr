<?php

namespace App\Http\Resources\Attempt;

use App\Modules\Attempt\Rules\AttemptAnnulledRules;
use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Http\Resources\TaskVariant\TaskVariantResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Attempt\Rules\AttemptSpeakingRules;

class AttemptMonitoringResource extends JsonResource
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
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'startedAt' => $this->started_at_local?->toIso8601String(),
            'finishedAt' => $this->finished_at_local?->toIso8601String(),
            'speakingFinishedAt' => $this->resource->speaking_finished_at,
            'speakingStartedAt' => $this->resource->speaking_started_at,
            'annulledAt' => $this->annulled_at,
            'tasks' => TaskVariantResource::collection($this->whenLoaded('taskVariants', fn () => $this->taskVariants)),
            'answers' => AttemptAnswerResource::collection($this->whenLoaded('answers')),
            'availability' => [
                'annul' =>  app(AttemptAnnulledRules::class)->check($this->resource)->available,
                'violations' => $this->resource->canEditViolation(),
                'speaking' => app(AttemptSpeakingRules::class)->get($this->resource)->available
            ],
        ];
    }
}
