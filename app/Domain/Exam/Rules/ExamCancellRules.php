<?php

namespace App\Domain\Exam\Rules;

use App\Domain\Shared\RuleResult;
use App\Models\Exam;

class ExamCancellRules{
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                code: "exam_has_been_already_cancelled",
                reason: "Экзамен уже отменен"
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                code: "exam_is_not_pending",
                reason: "Экзамен уже начался"
            );
        }

        return RuleResult::success();
    }
}