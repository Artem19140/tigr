<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Enums\TaskType;
use App\Exceptions\Attempt\AttemptAnswerValidationException;
use App\Models\AttemptAnswer;
use App\Models\Task;
use App\Models\TaskVariant;

class SingleInputTaskHandler
{
    public function for(Task $task): bool
    {
        return $task->type === TaskType::SingleInput;
    }

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
                'message' => 'not_valid_format',
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
}
