<?php

namespace App\Modules\Attempt\Rules;

use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Attempt;

class AttemptAnnulledRules
{
    public function check(Attempt $attempt):RuleResult
    {
        if($attempt->isAnnulled()){
            return  RuleResult::fail(
                AvailabilityCode::AttemptAnnulled
            );
        }

        if(! $attempt->created_at->isToday()){
            return  RuleResult::fail(
                'attempt_can_be_annuled_only_on_attempt_day'
            );
        }

        return RuleResult::success();
    }
}