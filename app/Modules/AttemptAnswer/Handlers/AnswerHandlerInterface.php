<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Models\AttemptAnswer;

interface AnswerHandlerInterface
{
    public function handle(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): void;
    
}