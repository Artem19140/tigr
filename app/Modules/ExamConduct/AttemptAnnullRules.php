<?php

namespace App\Modules\ExamConduct;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Attempt;

class AttemptAnnullRules
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