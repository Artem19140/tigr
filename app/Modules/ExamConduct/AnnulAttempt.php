<?php

namespace App\Modules\ExamConduct;

use App\Modules\ExamReview\FinilizeAttemptReview;
use App\Modules\ExamConduct\AttemptAnnullRules;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class AnnulAttempt
{
    public function __construct(
        protected FinilizeAttemptReview $finilizeAttemptReview,
        protected AttemptAnnullRules $attemptAnnullRules
    ) {}

    public function execute(
        Attempt $attempt,
        string $reason,
        Employee $employee
    ): void {
        DB::transaction(function () use ($attempt, $reason, $employee) {

            $result = $this->attemptAnnullRules->check($attempt);

            if(! $result->available){
                throw new BusinessException($result->code());
            }

            $this->finishAndIfNeededFinilize($attempt);
            $attempt->annul($reason, $employee->id);
            $attempt->save();
        });
    }

    protected function finishAndIfNeededFinilize(Attempt $attempt): void
    {
        if ($attempt->isFinished()) {
            return;
        }
        
        $attempt->finish();

        if ($attempt->canBeAutomaticallyFinalized()) {
            $this->finilizeAttemptReview->execute($attempt);
        }
    }
}
