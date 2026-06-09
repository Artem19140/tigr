<?php

namespace App\Http\Controllers\Web\Attempt;

use App\Domain\Attempt\Query\GetAttemptSpeakingTasksQuery;
use App\Exceptions\BusinessException;
use App\Http\Resources\Attempt\AttemptMonitoringResource;
use App\Http\Resources\Attempt\AttemptResource;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use App\Domain\Attempt\Rules\AttemptSpeakingRules;

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
        if(! $result->available){
            throw new BusinessException($result->reason);
        }
        // $this->ensureTodayIsExamDay($attempt);
        // $this->ensureAttemptHasSpeaking($attempt);

        // if (! $attempt->speaking_started_at) {
        //     throw new BusinessException('Говорение еще не начато');
        // }

        //$this->ensureSpeakingNotFinished($attempt);

        $attempt = $getAttemptSpeakingQuery->execute($attempt);

        return new AttemptMonitoringResource($attempt);
    }

    public function start(Attempt $attempt): JsonResource
    {
        $this->authorize($attempt);
        $result = $this->attemptSpeakingRules->start($attempt);
        if(! $result->available){
            throw new BusinessException($result->reason);
        }
        // $this->ensureTodayIsExamDay($attempt);
        // $this->ensureAttemptHasSpeaking($attempt);

        // if ($attempt->speaking_started_at) {
        //     throw new BusinessException('Говорение уже началось');
        // }
        $attempt->speaking_started_at = Carbon::now();
        $attempt->save();

        return new AttemptResource($attempt);
    }

    public function finish(Attempt $attempt): Response
    {
        $this->authorize($attempt);
        // $this->ensureTodayIsExamDay($attempt);
        // $this->ensureAttemptHasSpeaking($attempt);
        // $this->ensureSpeakingNotFinished($attempt);

        $result = $this->attemptSpeakingRules->finish($attempt);
        if(! $result->available){
            throw new BusinessException($result->reason);
        }
        
        $attempt->speaking_finished_at = Carbon::now();
        $attempt->save();

        return response()->noContent();
    }

    protected function authorize(Attempt $attempt): void
    {
        Gate::authorize('examiner', $attempt->exam);
    }

    protected function ensureSpeakingNotFinished(Attempt $attempt): void
    {
        if ($attempt->speaking_finished_at) {
            throw new BusinessException('Говорение уже завершено');
        }
    }

    protected function ensureAttemptHasSpeaking(Attempt $attempt): void
    {
        if (! $attempt->exam->hasSpeaking()) {
            throw new BusinessException('У данной попытки нет заданий на говорение');
        }
    }

    protected function ensureTodayIsExamDay(Attempt $attempt): void
    {
        if (! $attempt->exam->begin_time->isToday()) {
            throw new BusinessException('Говорение доступно в день экзамена');
        }
    }
}
