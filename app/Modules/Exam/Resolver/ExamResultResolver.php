<?php

namespace App\Modules\Exam\Resolver;

use App\Enums\ExamResultStatus;
use App\Models\Attempt;
use App\Models\Enrollment;
use App\Models\Exam;

class ExamResultResolver
{
    public function execute(
        Enrollment $enrollment,
        Exam $exam, 
        Attempt | null $attempt
    ): ?ExamResultStatus {

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
