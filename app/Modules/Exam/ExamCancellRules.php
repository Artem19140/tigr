<?php

namespace App\Modules\Exam;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Exam;

class ExamCancellRules{
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                BusinessCode::ExamAlreadyStarted
            );
        }

        return RuleResult::success();
    }
}