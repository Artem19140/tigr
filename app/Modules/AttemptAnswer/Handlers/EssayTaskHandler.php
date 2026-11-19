<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Enums\TaskType;
use App\Exceptions\AttemptAnswerValidationException;
use App\Models\AttemptAnswer;

class EssayTaskHandler implements AnswerHandlerInterface
{

    public function handle(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): void {
        $answer = $this->validate(
            $foreignNationalAnswer,
            $attemptAnswer
        );

        $attemptAnswer->update([
            'answer' => $answer
        ]);
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
                'type' => TaskType::Essay->value,
                'message' => 'not_valid_format',
                'foreign_national_answer' => 'foreignNationalAnswer',
            ]);
        }

        return $foreignNationalAnswer;
    }
}
