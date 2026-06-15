<?php

namespace App\Modules\Attempt\Rules;

use App\Modules\Shared\CodeTranslator;
use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Attempt;

class AttemptAnnulledRules
{
    public function __construct(
        protected CodeTranslator $translator
    ){}
    public function check(Attempt $attempt):RuleResult
    {
        if($attempt->isAnnulled()){
            return  RuleResult::fail(
                AvailabilityCode::AttemptAnnulled,
                $this->translator->translate(
                    AvailabilityCode::AttemptAnnulled
                )
            );
        }

        if(! $attempt->created_at->isToday()){
            return  RuleResult::fail(
                'attempt_can_be_banned_only_on_attempt_day',
                $this->translator->translate(
                    'attempt_can_be_banned_only_on_attempt_day'
                )
            );
        }

        return new RuleResult(true);
    }
}