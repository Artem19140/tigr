<?php

namespace App\Modules\Exam\Rules;

use App\Modules\Shared\CodeTranslator;
use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Exam;

class ProtocolCommentRules
{
    public function __construct(
        protected CodeTranslator $translator
    ){}
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled,
                $this->translator->translate(
                    AvailabilityCode::ExamCancelled
                )
            );
        }

        if($exam->isPending()){
            return RuleResult::fail(
                AvailabilityCode::ExamPending,
                $this->translator->translate(
                    AvailabilityCode::ExamPending
                )
            );
        }

        if(! $exam->begin_time->isToday()){
            return RuleResult::fail(
                'protocol_comment_edit_available_only_on_exam_day',
                $this->translator->translate(
                    'protocol_comment_edit_available_only_on_exam_day'
                )
            );
        }

        return RuleResult::success();
    }
}