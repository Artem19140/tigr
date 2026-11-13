<?php

namespace App\Modules\Attempt;

use App\Enums\BusinessCode;
use App\Models\Attempt;
use App\Modules\Shared\RuleResult;

class ActiveAttemptRules
{
    public function check(?Attempt $attempt): RuleResult 
    {
        if( ! $attempt ){
            return RuleResult::fail(
                BusinessCode::AttemptsNotExists
            );
        }   

        if( $attempt->isAnnulled() ){
            return RuleResult::fail(
                BusinessCode::AttemptAnnulled
            );
        }

        if( $attempt->isFinished() ){
            return RuleResult::fail(
                BusinessCode::AttemptAlreadyFinished
            );
        }

        if( $attempt->isExpired() ){
            return RuleResult::fail(
                BusinessCode::AttemptExpired
            );
        }

        return RuleResult::success();
    }
    
}