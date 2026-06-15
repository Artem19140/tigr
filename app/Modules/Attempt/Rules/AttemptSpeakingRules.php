<?php

namespace App\Modules\Attempt\Rules;

use App\Modules\Shared\CodeTranslator;
use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Attempt;

class AttemptSpeakingRules
{
    public function __construct(
        protected CodeTranslator $translator
    ){}
    public function start(Attempt $attempt): RuleResult
    {
        $failed = $this->baseCheck($attempt);

        if($failed){
            return $failed;
        }

        if($this->speakingStarted($attempt)){
            return RuleResult::fail(
                'attempt_has_been_already_started',
                $this->translator->translate(
                    'attempt_has_been_already_started'
                )
            );
        }
        return RuleResult::success();
    }

    public function finish(Attempt $attempt):RuleResult
    {
        $failed = $this->baseCheck($attempt);

        if($failed){
            return $failed;
        }

        if(! $this->speakingStarted($attempt)){
            return  RuleResult::fail(
                'attempt_speaking_is_not_started_yet',
                $this->translator->translate(
                    'attempt_speaking_is_not_started_yet'
                )
            );
        }

        if($this->speakingFinished($attempt)){
            return  RuleResult::fail(
                'attempt_speaking_is_already_finished',
                $this->translator->translate(
                    'attempt_speaking_is_already_finished'
                )
            );
        }
        return  RuleResult::success();
    }

    public function get(Attempt $attempt):RuleResult
    {
        $failed = $this->baseCheck($attempt);
        
        if($failed){
            return $failed;
        }
        
        if($this->speakingFinished($attempt)){
            return  RuleResult::fail(
                'attempt_speaking_is_already_finished',
                $this->translator->translate(
                    'attempt_speaking_is_already_finished'
                )
            );
        }

        return RuleResult::success();
    }

    protected function baseCheck(Attempt $attempt): RuleResult | null
    {
        if($this->hasNoSpeaking($attempt)){
            return  RuleResult::fail(
                'attempt_has_no_speaking',
                $this->translator->translate(
                    'attempt_has_no_speaking'
                )
            );
        }

        if($this->isNotToday($attempt)){
            return  RuleResult::fail(
                'attempt_speaking_available_on_attempt_passing_day',
                $this->translator->translate(
                    'attempt_speaking_available_on_attempt_passing_day'
                )
            );
        }

        if($attempt->isAnnulled()){
            return  RuleResult::fail(
                AvailabilityCode::AttemptAnnulled
            );
        }
        return null;
    }

    protected function hasNoSpeaking(Attempt $attempt): bool
    {
        return ! $attempt->exam->hasSpeaking();
    }

    protected function isNotToday(Attempt $attempt): bool
    {
        return ! $attempt->created_at->isToday();
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