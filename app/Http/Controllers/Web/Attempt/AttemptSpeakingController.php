<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\Query\GetAttemptSpeakingTasksQuery;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptMonitoringResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use App\Modules\Attempt\Rules\AttemptSpeakingRules;

class AttemptSpeakingController
{
    public function __construct(
        protected AttemptSpeakingRules $attemptSpeakingRules
    ){}
    public function show(
        Attempt $attempt,
        GetAttemptSpeakingTasksQuery $getAttemptSpeakingQuery
    ): JsonResource {
        $this->authorize($attempt);
        $result = $this->attemptSpeakingRules->get($attempt);
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }

        $attempt = $getAttemptSpeakingQuery->execute($attempt);

        return new AttemptMonitoringResource($attempt);
    }

    public function start(Attempt $attempt): JsonResource
    {
        $this->authorize($attempt);
        $result = $this->attemptSpeakingRules->start($attempt);
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }
        $attempt->speaking_started_at = Carbon::now();
        $attempt->save();

        return new AttemptResource($attempt);
    }

    public function finish(Attempt $attempt): Response
    {
        $this->authorize($attempt);

        $result = $this->attemptSpeakingRules->finish($attempt);
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }
        
        $attempt->speaking_finished_at = Carbon::now();
        $attempt->save();

        return response()->noContent();
    }

    protected function authorize(Attempt $attempt): void
    {
        Gate::authorize('examiner', $attempt->exam);
    }

}
