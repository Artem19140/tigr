<?php

namespace App\Modules\Attempt;

use App\Enums\BusinessCode;
use App\Modules\Attempt\FinilizeAttemptReview;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class FinishManualReview
{
    public function __construct(
        protected FinilizeAttemptReview $finilizeAttemptReview
    ) {}

    public function execute(Attempt $attempt): Attempt
    {
        $this->ensureNotChecked($attempt);
        $this->ensureAllManualReviewTasksChecked($attempt);
        $attempt = $this->finilizeAttemptReview
            ->execute($attempt);
        return $attempt;
    }

    protected function ensureNotChecked(Attempt $attempt): void
    {
        if ($attempt->isReviewed()) {
            Log::warning(BusinessCode::AttemptAlreadyReviwed->value, [
                'attempt_id' => $attempt->id,
            ]);
            
            throw new BusinessException(
                BusinessCode::AttemptAlreadyReviwed
            );
        }
    }

    protected function ensureAllManualReviewTasksChecked(Attempt $attempt): void
    {
        $notAllManualReviewTypes = $attempt->attemptAnswers()
            ->whereNull('reviewed_at')
            ->whereHas('taskVariant', function (Builder $query) {
                $query->whereHas('task', function (Builder $q) {
                    $q->manualReview();
                });
            })
            ->exists();
        if ($notAllManualReviewTypes) {
            throw new BusinessException(
                BusinessCode::UnreviewedAnswersExists
            );
        }
    }
}
