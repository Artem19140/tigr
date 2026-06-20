<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Enums\TaskType;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptMonitoringResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use App\Modules\Attempt\AttemptSpeakingRules;

class AttemptSpeakingController
{
    public function __construct(
        protected AttemptSpeakingRules $attemptSpeakingRules
    ){}
    public function show(
        Attempt $attempt
    ): JsonResource {
        $result = $this->attemptSpeakingRules->get($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }
        
        $attemptWithSpeaking = $this->loadSpeaking($attempt);

        return new AttemptMonitoringResource($attemptWithSpeaking);
    }

    public function start(Attempt $attempt): JsonResource
    {
        $result = $this->attemptSpeakingRules->start($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }
        
        $attempt->speaking_started_at = Carbon::now();
        $attempt->save();

        return new AttemptResource($attempt);
    }

    public function finish(Attempt $attempt): Response
    {
        $result = $this->attemptSpeakingRules->finish($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }
        
        $attempt->speaking_finished_at = Carbon::now();
        $attempt->save();

        return response()->noContent();
    }

    protected function loadSpeaking(Attempt $attempt)
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