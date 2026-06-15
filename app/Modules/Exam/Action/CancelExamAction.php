<?php

namespace App\Modules\Exam\Action;

use App\Modules\Exam\Rules\ExamCancellRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use Carbon\Carbon;

class CancelExamAction
{
    public function __construct(
        protected ExamCancellRules $examCancellRules
    ) {}

    public function execute(
        Exam $exam,
        string $reason
    ): void {

        $result = $this->examCancellRules->check($exam);

        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }

        $exam->cancelled_reason = $reason;
        $exam->cancelled_at = Carbon::now();
        $exam->save();
    }
}
