<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Modules\Attempt\GetSpeakingTasks;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptMonitoringResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use App\Modules\Attempt\AttemptSpeakingRules;

class AttemptSpeakingController
{
    public function __construct(
        protected AttemptSpeakingRules $attemptSpeakingRules
    ){}
    public function show(
        Attempt $attempt,
        GetSpeakingTasks $GetSpeakingTasks
    ): JsonResource {
        $result = $this->attemptSpeakingRules->get($attempt);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }
        
        $attempt = $GetSpeakingTasks->execute($attempt);

        return new AttemptMonitoringResource($attempt);
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
}