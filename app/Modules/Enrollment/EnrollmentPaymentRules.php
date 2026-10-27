<?php

namespace App\Modules\Enrollment;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Enrollment;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment): RuleResult
    {
        $exam = $enrollment->exam;

        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if($enrollment->attempt){
            return RuleResult::fail(
                BusinessCode::AttemptExists
            );
        }

        if($exam->codesTtlExpired()){
            return RuleResult::fail( 
                BusinessCode::ExamCodeExpired
            );
        }

        return RuleResult::success();
    }
}