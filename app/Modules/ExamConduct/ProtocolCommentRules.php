<?php

namespace App\Modules\ExamConduct;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Exam;

class ProtocolCommentRules
{
    public function check(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if($exam->isPending()){
            return RuleResult::fail(
                BusinessCode::ExamPending
            );
        }

        if($exam->protocolCommentEditTtlPast()){
            return RuleResult::fail(
                BusinessCode::ProtocolCommentEditUnavailable,
                [
                    'days' => Exam::PROTOCOL_COMMENT_EDIT_TTL_DAYS
                ]
            );
        }

        return RuleResult::success();
    }
}