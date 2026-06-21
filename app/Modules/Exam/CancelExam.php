<?php

namespace App\Modules\Exam;

use App\Modules\Exam\ExamCancellRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Support\Audit;
use Carbon\Carbon;

class CancelExam
{
    public function __construct(
        protected ExamCancellRules $examCancellRules,
        protected Audit $audit
    ) {}

    public function execute(
        Exam $exam,
        string $reason
    ): void {

        $result = $this->examCancellRules->check($exam);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        $exam->cancelled_reason = $reason;
        $exam->cancelled_at = Carbon::now();
        $exam->save();
        
        $this->audit->log(
            'delete',
            $exam,
            [
                'reason' => $exam->cancelled_reason
            ]
        );
    }
}
