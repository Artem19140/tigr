<?php

namespace App\Modules\AttemptAnswer;

use App\Modules\AttemptAnswer\TaskHandlerResolver;
use App\Exceptions\TaskAnswersNotAllowedException;
use App\Models\AttemptAnswer;
use App\Models\Task;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;

class HandleAttemptAnswer
{
    public function __construct(
        protected TaskHandlerResolver $taskHandlerResolver,
        protected Audit $audit,
        protected ModelAuditChangesBuilder $changesBuilder
    ) {}

    public function execute(
        mixed $answer,
        AttemptAnswer $attemptAnswer
    ): AttemptAnswer {

        $taskVariant = $attemptAnswer->taskVariant;
        $task = $taskVariant->task;

        $this->ensureTaskAllowedAnswers($task, $answer, $attemptAnswer);

        $handler = $this->taskHandlerResolver->resolve($task);

        $handler->handle(
            $answer, 
            $attemptAnswer
        );

        app()->terminating(
            function() use( $attemptAnswer ){
                $this->audit->log(
                    'updated_attempt_answer',
                    $attemptAnswer,
                    [
                        'changes' => $this->changesBuilder->build($attemptAnswer)
                    ]
                );
            }
        );

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
