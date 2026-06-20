<?php

namespace App\Modules\Enrollment;

use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Enrollment;
use App\Models\Exam;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment, Exam $exam):RuleResult
    {
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

        if($exam->isFinished()){
            return RuleResult::fail( 
                AvailabilityCode::ExamAlreadyFinished
            );
        }
        return RuleResult::success();
    }
}