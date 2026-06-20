<?php

namespace App\Modules\Exam;

use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Exam;

class ExamEditRules
{
    public function check(Exam $exam): RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                AvailabilityCode::ExamAlreadyStarted
            );
        }

        return RuleResult::success();
    }
}