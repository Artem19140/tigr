<?php

namespace App\Modules\AttemptAnswer;

use App\Modules\AttemptAnswer\Handlers\AnswerHandlerInterface;
use App\Modules\AttemptAnswer\Handlers\EssayTaskHandler;
use App\Modules\AttemptAnswer\Handlers\MultyInputTaskHandler;
use App\Modules\AttemptAnswer\Handlers\SingleChoiceTaskHandler;
use App\Modules\AttemptAnswer\Handlers\SingleInputTaskHandler;
use App\Enums\TaskType;
use App\Exceptions\TaskHandlerNotFoundException;
use App\Models\Task;

class TaskHandlerResolver
{
    public function resolve(Task $task)
    : AnswerHandlerInterface
    {
        return match ($task->type) {
            TaskType::SingleChoice => new SingleChoiceTaskHandler,
            TaskType::SingleInput => new SingleInputTaskHandler,
            TaskType::Essay => new EssayTaskHandler,
            TaskType::MultyInput => new MultyInputTaskHandler,
            default => throw new TaskHandlerNotFoundException([
                'task_id' => $task->id,
                'task_type' => $task->type,
            ])
        };
    }
}
