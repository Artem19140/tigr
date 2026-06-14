<?php

namespace App\Domain\Exam\Rules;

use App\Domain\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Exam;

class ProtocolCommentRules{
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(AvailabilityCode::ExamCancelled);
        }

        if($exam->isPending()){
            return RuleResult::fail(AvailabilityCode::ExamPending);
        }

        if(! $exam->begin_time->isToday()){
            return RuleResult::fail('protocol_comment_edit_available_only_on_exam_day');
        }

        return RuleResult::success();
    }
}