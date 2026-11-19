<?php

namespace App\Modules\Exam;

use App\Modules\Exam\ExamCancellRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use Carbon\Carbon;

class CancelExam
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
            throw new BusinessException(
                $result->code()
            );
        }

        $exam->update([
            'cancelled_reason' => $reason,
            'cancelled_at' => Carbon::now()
        ]);
    }
}
