<?php

namespace App\Domain\ExamDocument;

use App\Domain\Shared\RuleResult;
use App\Models\Exam;

class ExamDocumentAvailableResolver
{

    protected function available():RuleResult
    {
        return new RuleResult(
            available:true,
            code:null,
            reason:null
        );
    }

    protected function blocked(
        string $code,
        ?string $reason = null
    ) :RuleResult {
        return new RuleResult(
            available:false,
            code:$code,
            reason:$reason
        );
    }

    public function resolve(Exam $exam): array
    {
        return [
            'codes' => $this->codes($exam),
            'protocol' => $this->protocol($exam),
            'results' => $this->results($exam),
            'list' => $this->list($exam),
        ];
    }

    protected function list(Exam $exam)
    {

        if ($this->hasNoEnrollment($exam)) {
            return $this->blocked('no_enrollment');
        }

        return $this->available();
    }

    protected function codes(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked('cancelled'),
            $this->hasNoEnrollment($exam) => $this->blocked('no_enrollment'),
            ! $exam->canGenerateCodes() => $this->blocked('codes_generation_window_closed'),
            default => $this->available(),
        };
    }

    protected function protocol(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked('cancelled'),
            $exam->isPending() => $this->blocked('pending'),
            $this->hasNoEnrollment($exam) => $this->blocked('no_enrollment'),
            $this->hasNoAttempts($exam) => $this->blocked('no_attempts'),
            $this->hasActiveAttempts($exam) => $this->blocked('has_active_attempts'),
            default => $this->available(),
        };
    }

    protected function results(Exam $exam)
    {
        return match (true) {
            $exam->isCancelled() => $this->blocked('cancelled'),
            $exam->isPending() => $this->blocked('pending'),
            $this->hasNoEnrollment($exam) => $this->blocked('no_enrollment'),
            $this->hasNoAttempts($exam) => $this->blocked('no_attempts'),
            $this->hasActiveAttempts($exam) => $this->blocked('has_active_attempts'),
            $this->hasUncheckedAttemtps($exam) => $this->blocked('not_checked', 'на проверке'),
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
}
