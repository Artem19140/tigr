<?php

namespace App\Modules\Attempt;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinishAttempt
{
    public function __construct(
        protected FinilizeAttemptChecking $finilizeAttemptChecking
    ) {}

    public function execute(Attempt $attempt): void
    {
        $this->canFinish($attempt);

        DB::transaction(function () use ($attempt) {

            $attempt->finish();

            if ($attempt->canBeAutomaticallyFinalized()) {
                $this->finilizeAttemptChecking->execute($attempt);
            }

            $attempt->save();
        });
    }

    protected function canFinish(Attempt $attempt)
    {
        if ($attempt->isFinished()) {
            throw new BusinessException('Попытка уже завершена');
        }
        $minTimeMinutes = ExamSettings::attemptMinDurationMinutes();
        $now = Carbon::now();

        $attemptCanBeFinished = $attempt->started_at->copy()->addMinutes($minTimeMinutes);

        $tooEarlyToFinish = $now->lte($attemptCanBeFinished);
        if ($tooEarlyToFinish) {
            throw new BusinessException("Попытку возможно завершить минимум через  $minTimeMinutes минут после начала");
        }
    }
}
