<?php

namespace App\Modules\AttemptAnswer;

use App\Modules\AttemptAnswer\TaskHandlerResolver;
use App\Exceptions\Task\TaskAnswersNotAllowedException;
use App\Models\AttemptAnswer;
use App\Models\Task;

class HandleAttemptAnswer
{
    public function __construct(
        protected TaskHandlerResolver $taskHandlerResolver
    ) {}

    public function execute(
        mixed $answer,
        AttemptAnswer $attemptAnswer
    ): AttemptAnswer {

        $taskVariant = $attemptAnswer->taskVariant;
        $task = $taskVariant->task;

        $this->ensureTaskAllowedAnswers($task, $answer, $attemptAnswer);

        $handler = $this->taskHandlerResolver->resolve($task);

        $validatedAnswer = $handler->validate($answer, $attemptAnswer);
        if ($task->autoCheck()) {
            $mark = $handler->calculateMark($validatedAnswer, $taskVariant);
            $attemptAnswer->mark = $mark;
        }

        $attemptAnswer->answer = $validatedAnswer;
        $attemptAnswer->save();

        return $attemptAnswer;
    }

    protected function ensureTaskAllowedAnswers(
        Task $task,
        mixed $answer,
        AttemptAnswer $attemptAnswer
    ): void {
        if (! $task->type->hasAnswers()) {
            throw new TaskAnswersNotAllowedException([
                'task_id' => $task->id,
                'answer' => $answer,
                'attempt_answer_id' => $attemptAnswer->id,
            ]);
        }
    }
}
