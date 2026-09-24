<?php

namespace App\Modules\Exam;

use App\Models\Attempt;
use App\Models\Exam;

class ExamResultResolver
{
    public function execute(
        ?Attempt $attempt,
        Exam $exam,
    ): ?string {

        if($exam->isCancelled()){
            return null;
        }

        if($exam->isPending()){
            return null;
        }

        if (! $exam->codesTtlExpired()  && ! $attempt) {
            return null;
        }

        if ($exam->codesTtlExpired()  && ! $attempt) {
            return 'absent';
        }

        if ($attempt->isAnnulled()) {
            return 'annulled';
        }

        if ($attempt->is_passed === true) {
            return 'passed';
        }

        if ($attempt->is_passed === false) {
            return 'failed';
        }

        return null;
    }
}