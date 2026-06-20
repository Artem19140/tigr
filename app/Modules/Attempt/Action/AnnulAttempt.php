<?php

namespace App\Modules\Attempt\Action;

use App\Modules\Attempt\Rules\AttemptAnnulledRules;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class AnnulAttempt
{
    public function __construct(
        protected FinilizeAttemptChecking $finilizeAttemptChecking,
        protected AttemptAnnulledRules $attemptAnnulledRules
    ) {}

    public function execute(
        Attempt $attempt,
        string $annulledReason,
        Employee $employee
    ): void {
        DB::transaction(function () use ($attempt, $annulledReason, $employee) {

            $result = $this->attemptAnnulledRules->check($attempt);

            if(! $result->available){
                throw new BusinessException($result->message());
            }

            $this->finishAndIfNeededFinilize($attempt);
            $attempt->annul($annulledReason, $employee->id);
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
            $this->finilizeAttemptChecking->execute($attempt);
        }
    }
}
