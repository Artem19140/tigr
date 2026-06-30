<?php

namespace App\Modules\Exam;

use App\Enums\ExamResultStatus;
use App\Models\Enrollment;

class ExamResultResolver
{
    public function execute(
        Enrollment $enrollment,
        
    ): ?ExamResultStatus {
        $exam = $enrollment->exam;
        $attempt = $enrollment->attempt;
        if ((! $exam->isFinished() || $exam->isCancelled()) && ! $attempt) {
            return null;
        }

        if (! $attempt) {
            return ExamResultStatus::Absent;
        }

        if ($attempt->isAnnulled()) {
            return ExamResultStatus::Annulled;
        }

        if ($attempt->is_passed === true) {
            return ExamResultStatus::Passed;
        }

        if ($attempt->is_passed === false) {
            return ExamResultStatus::Failed;
        }

        return null;
    }
}
