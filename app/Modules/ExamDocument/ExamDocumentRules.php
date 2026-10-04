<?php

namespace App\Modules\ExamDocument;

use App\Modules\Shared\RuleResult;
use App\Enums\BusinessCode;
use App\Models\Exam;

class ExamDocumentRules
{
    public function list(Exam $exam):RuleResult
    {

        if ($exam->hasNoEnrollment()) {
            return RuleResult::fail(
                BusinessCode::EnrollmentNotExists
            );
        }

        return RuleResult::success();
    }

    public function codes(Exam $exam): RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                BusinessCode::ExamCancelled
            );
        }

        if($exam->hasNoEnrollment()){
            return RuleResult::fail(
                BusinessCode::EnrollmentNotExists
            );
        }

        if(! $exam->begin_time->isToday()){
            return RuleResult::fail(
                BusinessCode::CodesUnavailable
            );
        }

        if($exam->codesTtlExpired()){
            return RuleResult::fail(
                BusinessCode::CodesTtlExpired
            );
        }

        return RuleResult::success();
    }

    public function protocol(Exam $exam):RuleResult
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

        if($exam->hasNoEnrollment()){
            return RuleResult::fail(
                BusinessCode::EnrollmentNotExists
            );
        }

        if(
            $exam->enrollments_with_no_attempts_exists
                &&
            ! $exam->codesTtlExpired()
        ){
            return RuleResult::fail(
                BusinessCode::ExamCodeAliveAndEnrollmentsWithNoAttemptsExists
            );
        }

        if($exam->hasNoAttempts()){
            return RuleResult::fail(
                BusinessCode::AttemptsNotExists
            );
        }

        if($exam->hasActiveAttempts()){
            return RuleResult::fail(
                BusinessCode::ActiveAttemptsExists
            );
        }

        return RuleResult::success();
    }

    public function results(Exam $exam):RuleResult
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

        if($exam->hasNoEnrollment()){
            return RuleResult::fail(
                BusinessCode::EnrollmentNotExists
            );
        }

        if(
            $exam->enrollments_with_no_attempts_exists
                &&
            ! $exam->codesTtlExpired()
        ){
           return RuleResult::fail(
                BusinessCode::ExamCodeAliveAndEnrollmentsWithNoAttemptsExists
            );
        }

        if($exam->hasNoAttempts()){
            return RuleResult::fail(
                BusinessCode::AttemptsNotExists
            );
        }

        if($exam->hasActiveAttempts()){
            return RuleResult::fail(
                BusinessCode::ActiveAttemptsExists
            );
        }

        if($exam->hasUnreviewdAttemtps()){
            return RuleResult::fail(
                BusinessCode::ExamOnReview
            );
        }

        return RuleResult::success();
    }
}