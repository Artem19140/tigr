<?php

namespace App\Domain\Enrollment\Rules;

use App\Domain\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Enrollment;
use App\Models\Exam;

class EnrollmentPaymentRules
{
    public function check(Enrollment $enrollment, Exam $exam):RuleResult
    {
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

        if($enrollment->attempt){
            return RuleResult::fail(
                AvailabilityCode::AttemptExists
            );
        }

        return RuleResult::success();
    }
}