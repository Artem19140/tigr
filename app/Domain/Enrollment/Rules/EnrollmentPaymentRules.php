<?php

namespace App\Domain\Enrollment\Rules;

use App\Domain\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Enrollment;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment):RuleResult
    {
        $exam = $enrollment->exam;

        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if($exam->isFinished()){
            return RuleResult::fail(
                AvailabilityCode::ExamFinished
            );
        }

        if($enrollment->attempt_exists){
            return RuleResult::fail(
                AvailabilityCode::AttemptExists
            );
        }

        return RuleResult::success();
    }
}