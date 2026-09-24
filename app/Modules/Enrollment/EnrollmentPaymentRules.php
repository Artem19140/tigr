<?php

namespace App\Modules\Enrollment;

use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Enrollment;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment): RuleResult
    {
        $exam = $enrollment->exam;

        if($enrollment->attempt){
            return RuleResult::fail(
                AvailabilityCode::AttemptExists
            );
        }

        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if($exam->codesTtlExpired()){
            return RuleResult::fail( 
                AvailabilityCode::ExamCodeExpired
            );
        }

        return RuleResult::success();
    }
}