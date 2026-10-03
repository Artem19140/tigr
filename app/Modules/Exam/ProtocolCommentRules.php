<?php

namespace App\Modules\Exam;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Exam;

class ProtocolCommentRules
{
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if($exam->isPending()){
            return RuleResult::fail(
                BusinessCode::ExamPending
            );
        }

        if(! $exam->begin_time->isToday()){
            return RuleResult::fail(
                BusinessCode::ProtocolCommentEditUnavailable
            );
        }

        return RuleResult::success();
    }
}