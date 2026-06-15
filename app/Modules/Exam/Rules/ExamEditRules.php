<?php

namespace  App\Modules\Exam\Rules;

use App\Modules\Shared\CodeTranslator;
use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Exam;

class ExamEditRules
{
    public function __construct(
        protected CodeTranslator $translator
    ){}
    public function check(Exam $exam): RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled,
                $this->translator->translate(
                    AvailabilityCode::ExamCancelled
                )
            );
        }

        if(! $exam->isPending()){
            return RuleResult::fail(
                AvailabilityCode::ExamStarted,
                $this->translator->translate(
                    AvailabilityCode::ExamStarted
                )
            );
        }

        return RuleResult::success();
    }
}