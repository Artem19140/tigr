<?php

namespace App\Modules\Attempt;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Attempt;

class AttemptAnnulledRules
{
    public function check(Attempt $attempt):RuleResult
    {
        if($attempt->isAnnulled()){
            return  RuleResult::fail(
                BusinessCode::AttemptAnnulled
            );
        }

        if(! $attempt->created_at->isToday()){
            return  RuleResult::fail(
                BusinessCode::AttemptAnnulmentUnavailable
            );
        }

        return RuleResult::success();
    }
}