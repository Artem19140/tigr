<?php

namespace App\Domain\Attempt\Action;

use App\Domain\Attempt\Rules\AttemptBanRules;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class BanAttemptAction
{
    public function __construct(
        protected FinilizeAttemptCheckingAction $finilizeAttemptCheckingAction,
        protected AttemptBanRules $attemptBanRules
    ) {}

    public function execute(
        Attempt $attempt,
        string $banReason,
        Employee $employee
    ): void {
        DB::transaction(function () use ($attempt, $banReason, $employee) {

            $result = $this->attemptBanRules->check($attempt);

            if(! $result->available){
                throw new BusinessException($result->reason);
            }

            $this->finishAndIfNeededFinilize($attempt);

            $attempt->ban_reason = $banReason;
            $attempt->ban_by_id = $employee->id;
            $attempt->ban();
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
            $this->finilizeAttemptCheckingAction->execute($attempt);
        }
    }
}
