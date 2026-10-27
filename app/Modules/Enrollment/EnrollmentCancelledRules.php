<?php

namespace App\Modules\Enrollment;

use App\Enums\BusinessCode;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Shared\RuleResult;

final class EnrollmentCancelledRules
{
    public function check(
        Enrollment $enrollment,
        Exam $exam
    ): RuleResult {

        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                BusinessCode::ExamAlreadyStarted
            );
        }

        if($enrollment->isCancelled()){
            return RuleResult::fail(
                BusinessCode::EnrollmentCancelled
            );
        }

        return RuleResult::success();
    }
    
}