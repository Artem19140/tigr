<?php

namespace App\Modules\Attempt;

use App\Enums\TaskType;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GetSpeakingTasks
{
    public function execute(Attempt $attempt): Attempt
    {
        $attempt->loadMissing([
            'taskVariants' => function (BelongsToMany $query) use ($attempt) {
                $query->whereHas('task', function (Builder $q) {
                    $q->where('type', TaskType::Speaking);
                })->with([
                    'task',
                    'answers',
                    'attemptsAnswer' => function ($query) use ($attempt) {
                        $query->where('attempt_id', $attempt->id);
                    },
                ]);
            },
        ]);
        $attempt->taskVariants = $attempt->taskVariants->sortBy('task.order');

        return $attempt;
    }
}
