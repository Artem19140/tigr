<?php

namespace App\Modules\Scheduler;

use App\Models\Attempt;
use App\Modules\Attempt\FinilizeAttemptChecking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CloseAbandonedAttempts
{
    public function __construct(
        protected FinilizeAttemptChecking $finilizeAttemptChecking
    ) {}

    public function execute(): void
    {
        $now = Carbon::now();

        Attempt::query()
            ->where('expired_at', '<=', $now)
            ->with(['exam.type'])
            ->active()
            ->get()
            ->each(function ($attempt) {
                $this->close($attempt);
            });
    }

    protected function close(Attempt $attempt): void
    {
        if ($attempt->finished_at !== null) {
            Log::warning('cron get attempt with not null finished_at', [
                'attempt_id' => $attempt->id
            ]);
            return;
        }
        $attempt->finished_at = $attempt->last_activity_at;
        if ($attempt->canBeAutomaticallyFinalized()) {
            $this->finilizeAttemptChecking->execute($attempt);
        }

        $attempt->save();
        $this->log($attempt);
    }

    protected function log(Attempt $attempt): void
    {
        Log::info('attempt_closed_by_cron', [
            'attempt_id' => $attempt->id
        ]);
    }
}
