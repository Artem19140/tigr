<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Enums\TaskType;
use App\Exceptions\AttemptAnswerValidationException;
use App\Models\AttemptAnswer;
use App\Models\TaskVariant;

class SingleInputTaskHandler implements AnswerHandlerInterface
{
    public function validate(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): string {
        if(! $foreignNationalAnswer){
            return '';
        }
        if (! \is_string($foreignNationalAnswer)) {
            throw new AttemptAnswerValidationException([
                'attempt_answer_id' => $attemptAnswer->id,
                'type' => TaskType::SingleInput,
                'message' => 'Not valid format',
                'answer' => $foreignNationalAnswer
            ]);
        }

        return $foreignNationalAnswer;
    }

    public function calculateMark(
        string $foreignNationalAnswer,
        TaskVariant $taskVariant
    ): int {
        
        $etalonAnswers = $taskVariant->answers->pluck('content');

        $normalizedEtalonAnswers = $etalonAnswers->map(function ($item) {
            return $this->normalize($item);
        })->toArray();

        $normalizedForeignAnswer = $this->normalize($foreignNationalAnswer);

        $answersMatches = \in_array($normalizedForeignAnswer, $normalizedEtalonAnswers);

        return $answersMatches ? $taskVariant->task->mark : 0;
    }

    protected function normalize(string $value): string
    {
        return mb_strtolower(trim($value), 'UTF-8');
    }

    public function handle(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): void {
        $answer = $this->validate(
            $foreignNationalAnswer,
            $attemptAnswer
        );

        $mark = $this->calculateMark(
            $answer,
            $attemptAnswer->taskVariant
        );

        $attemptAnswer->update([
            'answer' => $answer,
            'mark' => $mark
        ]);
    }
}
