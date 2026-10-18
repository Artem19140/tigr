<?php

namespace App\Modules\Scheduler;

use App\Models\Attempt;
use App\Modules\ExamReview\FinilizeAttemptReview;
use App\Support\Audit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class CloseAbandonedAttempts
{
    public function __construct(
        protected FinilizeAttemptReview $finilizeAttemptReview,
        protected Audit $audit
    ) {}

    public function execute(): void
    {
        $now = Carbon::now();

        Attempt::query()
            ->where('expired_at', '<', $now)
            ->where('reviewed_at', null)
            ->with(['exam.type'])
            ->whereHas('exam.type', function(Builder $query){
                $query->where('need_human_review', false);
            })
            
            
            ->get()
            ->each(function (Attempt $attempt) {
                $this->close($attempt);
            });
    }

    protected function close(Attempt $attempt): void
    {
       
        if ($attempt->finished_at === null) {
            $attempt->finished_at = $attempt->last_activity_at;
        }

        if ($attempt->canBeAutomaticallyFinalized()) {
            $this->finilizeAttemptReview->execute($attempt);
        }else{
            Log::warning('Tries to close automatically by cron attempt with human review', [
                'attempt_id' => $attempt->id
            ]);
        }

        $attempt->save();

        $this->audit->log(
            'attempt_closed_by_cron',
            $attempt,
        );
    }
}
