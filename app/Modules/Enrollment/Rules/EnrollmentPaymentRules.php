<?php

namespace App\Modules\Enrollment\Rules;

use App\Modules\Shared\CodeTranslator;
use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Enrollment;
use App\Models\Exam;

class EnrollmentPaymentRules
{
    public function __construct(
        protected CodeTranslator $translator
    ){}
    public function check(Enrollment $enrollment, Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled,
                $this->translator->translate(
                    AvailabilityCode::ExamCancelled
                )
            );
        }

        if($exam->isFinished()){
            return RuleResult::fail(
                AvailabilityCode::ExamFinished,
                $this->translator->translate(
                    AvailabilityCode::ExamFinished
                )
            );
        }

        if($enrollment->attempt){
            return RuleResult::fail(
                AvailabilityCode::AttemptExists,
                $this->translator->translate(
                    AvailabilityCode::AttemptExists
                )
            );
        }

        return RuleResult::success();
    }
}