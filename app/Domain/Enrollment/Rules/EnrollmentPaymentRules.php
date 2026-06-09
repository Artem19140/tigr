<?php

namespace App\Domain\Enrollment\Rules;

use App\Domain\Shared\RuleResult;
use App\Models\Enrollment;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment):RuleResult
    {
        $exam = $enrollment->exam;

        if($exam->isCancelled()){
            return new RuleResult(
                available:false,
                code:'exam_cancelled',
                reason:'Нельзя сменить оплату, если экзамен отменен'
            );
        }

        if($exam->isFinished()){
            return new RuleResult(
                available: false,
                code:'exam_finished',
                reason:'Нельзя сменить оплату, если экзамен завершен'
            );
        }

        if($enrollment->attempt_exists){
            return new RuleResult(
                available:false,
                code:'attempt_exists',
                reason:'Нельзя сменить оплату, если существует попытка экзамена'
            );
        }

        return new RuleResult(available:true);
    }
}