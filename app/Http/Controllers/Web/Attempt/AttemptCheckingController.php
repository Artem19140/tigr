<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\Action\Checking\FinishAttemptManualCheckingAction;
use App\Enums\TaskType;
use App\Http\Resources\Attempt\AttemptCheckingResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class AttemptCheckingController
{
    public function show(Attempt $attempt): JsonResource
    {
        $this->authorize($attempt);
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
        $this->authorize($attempt);

        $attempt = $finishAttemptManualCheckingAction
            ->execute($attempt);

        return response()->json([
            'attempt' => new AttemptResource($attempt),
        ]);
    }

    protected function authorize(Attempt $attempt)
    {
        Gate::authorize('examiner', $attempt->exam);
    }
}
