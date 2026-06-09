<?php

namespace App\Domain\Attempt\Rules;

use App\Domain\Shared\RuleResult;
use App\Models\Attempt;

class AttemptSpeakingRules
{
    public function start(Attempt $attempt): RuleResult
    {
        $failed = $this->baseCheck($attempt);

        if($failed){
            return $failed;
        }

        if($this->speakingStarted($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_has_been_already_started',
                reason: 'Говорение уже начато'
            );
        }
        return new RuleResult(true);
    }

    public function finish(Attempt $attempt):RuleResult
    {
        $failed = $this->baseCheck($attempt);

        if($failed){
            return $failed;
        }

        if(! $this->speakingStarted($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_is_not_started',
                reason: 'Говорение еще начато'
            );
        }

        if($this->speakingFinished($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_is_already_finished',
                reason: 'Говорение уже завершено'
            );
        }

        return new RuleResult(true);
    }

    public function get(Attempt $attempt):RuleResult
    {
        $failed = $this->baseCheck($attempt);
        if($failed){
            return $failed;
        }
        
        if($this->speakingFinished($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_is_already_finished',
                reason: 'Говорение уже завершено'
            );
        }

        return new RuleResult(true);
    }

    protected function baseCheck(Attempt $attempt): RuleResult | null
    {
        if($this->hasNoSpeaking($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_has_no_speaking',
                reason: 'У данной попытки нет заданий на говорение'
            );
        }

        if($this->isNotToday($attempt)){
            return new RuleResult(
                available: false,
                code: 'attempt_passing_is_not_today',
                reason: 'Говорение доступно в день экзамена'
            );
        }
        return null;
    }

    protected function hasNoSpeaking(Attempt $attempt): bool
    {
        return !$attempt->exam->hasSpeaking();
    }

    protected function isNotToday(Attempt $attempt): bool
    {
        return ! $attempt->exam->begin_time->isToday();
    }

    protected function speakingStarted(Attempt $attempt): bool
    {
        return $attempt->speaking_started_at !== null;
    }

    protected function speakingFinished(Attempt $attempt): bool
    {
        return $attempt->speaking_finished_at !== null;
    }
}