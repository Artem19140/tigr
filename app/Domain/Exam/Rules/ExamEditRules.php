<?php

namespace  App\Domain\Exam\Rules;

use App\Domain\Shared\RuleResult;
use App\Models\Exam;

class ExamEditRules{
    public function check(Exam $exam): RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                code:'exam_is_cancelled',
                reason:'Экзамен отменен'
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                code:'exam_is_not_pending',
                reason:'Экзамен возмножно редактировать до его начала'
            );
        }

        return RuleResult::success();
    }
}