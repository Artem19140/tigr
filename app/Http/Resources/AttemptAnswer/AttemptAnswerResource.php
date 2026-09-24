<?php

namespace App\Http\Resources\AttemptAnswer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $needReview = $request->routeIs(
            'attempts.review', 
            'attempts.speaking.show'
        );
        
        return [
            'id' => $this->id,
            'answer' => $this->answer,
            
            $this->mergeWhen($needReview, [
                'checkedAt' => $this->checked_at,
                'mark' => $this->mark,
                'rateUrl' => $this->rateUrl(),
            ]),
            $this->mergeWhen($request->routeIs('attempts.show'),[
                'updateUrl' =>   $this->updateUrl(),
                'audioPlayedUrl' => $this->audioPlayedUrl(),
                'audioPlayedAt' => $this->audio_played_at,
            ])
        ];
    }

    protected function rateUrl(): string {
        return route('attempts.answers.rate', [
            'attempt' => $this->resource->attempt_id,
            'attempt_answer' => $this->resource->id
        ], false);
    }

    protected function updateUrl(): string {
        return route('attempts.answers.update', [
            'attempt' => $this->resource->attempt_id,
            'attempt_answer' => $this->resource->id
        ], false);
    }

    protected function audioPlayedUrl(): string {
        return route('attempts.answers.audio.update', [
            'attempt' => $this->resource->attempt_id,
            'attempt_answer' => $this->resource->id
        ], false);
    }
    
}
