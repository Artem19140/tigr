<?php

namespace App\Domain\Attempt\Action;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BanAttemptAction
{
    public function __construct(
        protected FinilizeAttemptCheckingAction $finilizeAttemptCheckingAction
    ) {}

    public function execute(
        Attempt $attempt,
        string $banReason,
        Employee $employee
    ): void {
        DB::transaction(function () use ($attempt, $banReason, $employee) {

            $this->ensureNotBanned($attempt);

            $this->finishAndIfNeededFinilize($attempt);

            $attempt->ban_reason = $banReason;
            $attempt->ban_by_id = $employee->id;
            $attempt->ban();
            $attempt->save();
        });
    }

    protected function ensureNotBanned(Attempt $attempt): void
    {
        if ($attempt->isBanned()) {
            Log::warning(' repeated cancellation of attempt ', [
                'attempt_id' => $attempt->id,
            ]);
            throw new BusinessException('Попытка аннулирована');
        }
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
