<?php

namespace App\Modules\Attempt\Query;

use App\Enums\TaskType;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GetCurrentAttemptQuery
{
    public function execute(Attempt $attempt): Attempt
    {
        if (! $attempt->isStarted()) {
            throw new BusinessException('Попытка еще не начата ');
        }

        $attempt->load([
            'taskVariants' => function (BelongsToMany $query) {
                $query->whereHas('task', function (Builder $q) {
                    $q->where('type', '<>', TaskType::Speaking);
                });
            },
            'taskVariants.task',
            'taskVariants.answers' => fn (HasMany $q) => $q->orderBy('order'),
            'taskVariants.attemptsAnswer' => function ($query) use ($attempt) {
                $query->where('attempt_id', $attempt->id);
            },
            'exam.type',
            'foreignNational',
        ]);
        $attempt->taskVariants = $attempt->taskVariants->sortBy('task.order');

        return $attempt;

    }
}
