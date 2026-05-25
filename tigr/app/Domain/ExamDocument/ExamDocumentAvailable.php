<?php

namespace App\Domain\ExamDocument;

use App\Domain\Exam\Guard\ExamGuard;
use App\Exceptions\BusinessException;
use App\Models\Exam;

class ExamDocumentAvailable
{
    public function __construct(
        protected ExamGuard $examGuard,
    ) {}

    public function codes(Exam $exam)
    {
        $this->examGuard->ensureNotCancelled($exam);
        $this->examGuard->ensureNotFinished($exam);
        $this->ensureEnrollmentsExists($exam);
        if (! $exam->canGenerateCodes()) {
            $minutes = Exam::CODES_TTL_AFTER_BEGIN_MINUTES;
            throw new BusinessException("Коды возможно сформировать в день экзамена и в течении $minutes минут после его начала");
        }
    }

    public function list(Exam $exam): void
    {
        $this->ensureEnrollmentsExists($exam);
    }

    public function protocol(Exam $exam): void
    {
        $this->examGuard->ensureNotCancelled($exam);
        $this->ensureEnrollmentsExists($exam);
    }

    public function results(Exam $exam): void
    {
        $this->examGuard->ensureNotCancelled($exam);
        $this->ensureEnrollmentsExists($exam);
        $this->ensureAllAttemptsChecked($exam);
    }

    protected function ensureAllAttemptsChecked(Exam $exam): void
    {
        $uncheckedAttemptsExists = $exam->attempts()
            ->unchecked()
            ->exists();
        if ($uncheckedAttemptsExists) {
            throw new BusinessException('Не все попытки проверены');
        }
    }

    protected function ensureEnrollmentsExists(Exam $exam): void
    {
        if (! $exam->enrollments()->exists()) {
            throw new BusinessException('На экзамен не записано ни одного ИГ');
        }
    }
}
