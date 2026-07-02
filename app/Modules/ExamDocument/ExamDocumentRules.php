<?php

namespace App\Modules\ExamDocument;

use App\Modules\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Employee;
use App\Models\Exam;

class ExamDocumentRules
{
    protected function available():RuleResult
    {
        return RuleResult::success();
    }

    protected function blocked(
        AvailabilityCode | string $code,
    ): RuleResult {
        return RuleResult::fail(
            $code
        );
    }

    public function resolve(
        Exam $exam, 
        Employee $employee
    ): array {
        $rules = [];
        if($employee->can('examiner', $exam)){
            $rules['codes'] =  $this->codes($exam)->toArray();
        }
        if($employee->can('protocol', $exam)){
            $rules['protocol'] = $this->protocol($exam)->toArray();
        }
        if($employee->can('results', $exam)){
            $rules['results'] = $this->results($exam)->toArray();
        }
        if($employee->can('list', $exam)){
            $rules['list'] = $this->list($exam)->toArray();
        }
        return $rules;
    }

    public function list(Exam $exam):RuleResult
    {

        if ($this->hasNoEnrollment($exam)) {
            return RuleResult::fail(
                AvailabilityCode::EnrollmentNotExists
            );
        }

        return RuleResult::success();
    }

    public function codes(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if($this->hasNoEnrollment($exam)){
            return RuleResult::fail(
                AvailabilityCode::EnrollmentNotExists
            );
        }

        if(! $exam->begin_time->isToday()){
            return RuleResult::fail(
                'codes_available_only_on_exam_day'
            );
        }

        if($exam->codesTtlExpired()){
            return RuleResult::fail(
                'codes_ttl_expired'
            );
        }

        return RuleResult::success();
    }

    public function protocol(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if($exam->isPending()){
            return RuleResult::fail(
                AvailabilityCode::ExamPending
            );
        }

        if($this->hasNoEnrollment($exam)){
            return RuleResult::fail(
                AvailabilityCode::EnrollmentNotExists
            );
        }

        if($this->hasNoAttempts($exam)){
            return RuleResult::fail(
                AvailabilityCode::AttemptsNotExists
            );
        }

        if($this->hasActiveAttempts($exam)){
            return RuleResult::fail(
                AvailabilityCode::ActiveAttemptsExists
            );
        }

        return RuleResult::success();
    }

    public function results(Exam $exam):RuleResult
    {
        if($exam->isCancelled()){
            return RuleResult::fail(
                AvailabilityCode::ExamCancelled
            );
        }

        if($exam->isPending()){
            return RuleResult::fail(
                AvailabilityCode::ExamPending
            );
        }

        if($this->hasNoEnrollment($exam)){
            return RuleResult::fail(
                AvailabilityCode::EnrollmentNotExists
            );
        }

        if($this->hasNoAttempts($exam)){
            return RuleResult::fail(
                AvailabilityCode::AttemptsNotExists
            );
        }

        if($this->hasActiveAttempts($exam)){
            return RuleResult::fail(
                AvailabilityCode::ActiveAttemptsExists
            );
        }

        if($this->hasUncheckedAttemtps($exam)){
            return RuleResult::fail(
                'exam_on_checking'
            );
        }

        return RuleResult::success();
    }

    protected function hasNoEnrollment(Exam $exam): bool
    {
        return ! $exam->enrollments_exists;
    }

    protected function hasNoAttempts(Exam $exam): bool
    {
        return ! $exam->attempts_exists;
    }

    protected function hasUncheckedAttemtps(Exam $exam): bool
    {
        return $exam->unchecked_attempts_exists;
    }

    protected function hasActiveAttempts(Exam $exam): bool
    {
        return $exam->active_attempts_exists;
    }
}
