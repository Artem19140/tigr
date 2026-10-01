<?php

namespace App\Http\Resources\TaskVariant;

use App\Http\Resources\Answer\AnswerResource;
use App\Http\Resources\AttemptAnswer\AttemptAnswerResource;
use App\Models\AttemptAnswer;
use App\Models\Employee;
use App\Models\ForeignNational;
use App\Modules\TaskVariant\ContentTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskVariantResource extends JsonResource
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
            // 'content' => $this->transformContent(
            //     $this->content,
            //     $this->attemptAnswers,
            //     $request->user()
            // ),

            'content' =>app(ContentTransformer::class)
                ->transform(
                    $this->content,
                    $this->attemptAnswers,
                    $request->user()
                ),

            'order' => $this->whenLoaded('task', fn () => $this->task->order),
            'type' => $this->whenLoaded('task', fn () => $this->task->type),
            'mark' => $this->whenLoaded('task', fn () => $this->task->mark),

            'answers' => AnswerResource::collection(
                $this->whenLoaded('answers')
            ),

            'attemptAnswer' => $this->whenLoaded(
                'attemptAnswers', 
                fn () => new AttemptAnswerResource($this->attemptAnswers)
            ),

            'description' => $this->whenLoaded('task', fn () => $this->task->description),
            'postscriptum' => $this->whenLoaded('task', fn () => $this->task->postscriptum),

            'groupNumber' => $this->group_number,
            'fipiNumber' => $this->fipi_number
        ];
    }

    protected function transformContent(
        array $content,
        AttemptAnswer $attemptAnswer,
        Employee|ForeignNational $actor
    ): array {
        foreach ($content as $key => $block) {
            if (
                ! \is_array($block) ||
                ($block['type'] ?? null) !== 'audio'
            ) {
                continue;
            }

            $audioNotPlayed = $attemptAnswer->audio_played_at === null;
            $isExaminer = $actor instanceof Employee;

            if ($isExaminer) {

                $block['audioPlayUrl'] = null;

                $block['url'] = Storage::disk('public')
                    ->url($block['value']);

            } else {

                $block['audioPlayUrl'] = $audioNotPlayed
                    ? route('attempts.answers.audio.update', [
                        'attempt' => $attemptAnswer->attempt_id,
                        'attempt_answer' => $attemptAnswer,
                    ])
                    : null;

                $block['url'] = $audioNotPlayed
                    ? Storage::disk('public')->url($block['value'])
                    : null;

            }

            $block = $this->transformContent(
                $block,
                $attemptAnswer,
                $actor
            );

            $content[$key] = $block;
        }

        return $content;
    }
}