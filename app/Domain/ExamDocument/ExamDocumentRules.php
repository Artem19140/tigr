<?php

namespace App\Domain\ExamDocument;

use App\Domain\Shared\RuleResult;
use App\Enums\AvailabilityCode;
use App\Models\Employee;
use App\Models\Exam;

class ExamDocumentRules
{
    protected function available():RuleResult
    {
        return new RuleResult(
            available:true,
            code:null
        );
    }

    protected function blocked(
        AvailabilityCode | string $code,
    ): RuleResult {
        return RuleResult::fail(
            $code
        );
    }

    public function resolve(Exam $exam,Employee $employee): array
    {
        // $rules = [];
        // if($employee->can('codes', $exam)){
        //     $rules['codes'] = $this->codes($exam);
        // }
        // if($employee->can('protocol', $exam)){
        //     $rules['protocol'] = $this->protocol($exam);
        // }
        // if($employee->can('results', $exam)){
        //     $rules['results'] = $this->results($exam);
        // }
        // if($employee->can('list', $exam)){
        //     $rules['list'] = $this->list($exam);
        // }
        // return $rules;
        return [
            'codes' => $this->codes($exam),
            'protocol' => $this->protocol($exam),
            'results' => $this->results($exam),
            'list' => $this->list($exam),
        ];
    }

    public function list(Exam $exam)
    {

        if ($this->hasNoEnrollment($exam)) {
            return $this->blocked(AvailabilityCode::EnrollmentNotExists);
        }

        return $this->available();
    }

    public function codes(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked(AvailabilityCode::ExamCancelled),
            $this->hasNoEnrollment($exam) => $this->blocked(AvailabilityCode::EnrollmentNotExists),
            ! $exam->begin_time->isToday() => $this->blocked('codes_available_only_on_exam_day'),
            $this->codesExpired($exam) => $this->blocked('codes_ttl_expired'),
            default => $this->available(),
        };
    }

    public function protocol(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked(AvailabilityCode::ExamCancelled),
            $exam->isPending() => $this->blocked(AvailabilityCode::ExamPending),
            $this->hasNoEnrollment($exam) => $this->blocked(AvailabilityCode::EnrollmentNotExists),
            $this->hasNoAttempts($exam) => $this->blocked(AvailabilityCode::AttemptsNotExists),
            $this->hasActiveAttempts($exam) => $this->blocked(AvailabilityCode::ActiveAttemptsExists),
            default => $this->available(),
        };
    }

    public function results(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked(AvailabilityCode::ExamCancelled),
            $exam->isPending() => $this->blocked(AvailabilityCode::ExamPending),
            $this->hasNoEnrollment($exam) => $this->blocked(AvailabilityCode::EnrollmentNotExists),
            $this->hasNoAttempts($exam) => $this->blocked(AvailabilityCode::AttemptsNotExists),
            $this->hasActiveAttempts($exam) => $this->blocked(AvailabilityCode::ActiveAttemptsExists),
            $this->hasUncheckedAttemtps($exam) => $this->blocked('exam_on_checking'),
            default => $this->available(),
        };
    }

    protected function hasNoEnrollment(Exam $exam): bool
    {
        return $exam->enrollments_count === 0;
    }

    protected function hasNoAttempts(Exam $exam): bool
    {
        return ! $exam->has_attempts;
    }

    protected function hasUncheckedAttemtps(Exam $exam): bool
    {
        return $exam->has_unchecked_attempts;
    }

    protected function hasActiveAttempts(Exam $exam): bool
    {
        return $exam->has_active_attempts;
    }

    protected function codesExpired(Exam $exam){
        $deadline = $exam->begin_time
            ->copy()
            ->addMinutes(Exam::CODES_TTL_AFTER_BEGIN_MINUTES);
        return now()->gte($deadline);
    }
}
