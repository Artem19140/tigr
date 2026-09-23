<?php

namespace App\Http\Resources\Attempt;

use App\Modules\Attempt\AttemptAnnulledRules;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Attempt\AttemptSpeakingRules;

class AttemptConductResource extends JsonResource
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
            'availability' => [
                'annul' =>  app(AttemptAnnulledRules::class)->check($this->resource)->available,
                'speaking' => app(AttemptSpeakingRules::class)->get($this->resource)->available
            ],
            'actions' => [
                'destroy' =>  [
                    'url' => route('attempts.destroy',[
                        'attempt' => $this->resource
                    ], false),
                    'disabled' => ! app(AttemptAnnulledRules::class)->check($this->resource)->available
                ],
                'speaking' => [
                    'url' => route('attempts.speaking.show', [
                        'attempt' => $this->resource
                    ], false),
                    'disabled' => ! app(AttemptSpeakingRules::class)->get($this->resource)->available
                ]
            ]
        ];
    }
}
