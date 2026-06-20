<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\FinishAttemptManualCheckingAction;
use App\Enums\TaskType;
use App\Http\Resources\Attempt\AttemptCheckingResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class AttemptCheckingController
{
    public function show(Attempt $attempt): JsonResource
    {

        $attempt->load([
            'taskVariants' => function (BelongsToMany $query) {
                $query->whereHas('task', function (Builder $q) {
                    $q->whereIn('type', TaskType::manualCheckTypes());
                });
            },
            'taskVariants.answers',
            'taskVariants.task',
            'taskVariants.attemptsAnswer' => function ($query) use ($attempt) {
                $query->where('attempt_id', $attempt->id);
            },
        ]);
        $attempt->taskVariants = $attempt
            ->taskVariants
            ->sortBy('task.order');

        return new AttemptCheckingResource($attempt);
    }

    public function finish(
        Attempt $attempt,
        FinishAttemptManualCheckingAction $finishAttemptManualCheckingAction
    ): JsonResponse {

        $attempt = $finishAttemptManualCheckingAction
            ->execute($attempt);

        return response()->json([
            'attempt' => new AttemptResource($attempt),
        ]);
    }
}
