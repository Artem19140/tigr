<?php

namespace App\Modules\Attempt;

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
        if ($attempt->isChecked()) {
            Log::warning('trying to repeat to finish attempt Review', [
                'attempt_id' => $attempt->id,
            ]);
            throw new BusinessException('Попытка уже проверена');
        }
    }

    protected function ensureAllManualReviewTasksChecked(Attempt $attempt): void
    {
        $notAllManualReviewTypes = $attempt->attemptAnswers()
            ->whereNull('checked_at')
            ->whereHas('taskVariant', function (Builder $query) {
                $query->whereHas('task', function (Builder $q) {
                    $q->manualReview();
                });
            })
            ->exists();
        if ($notAllManualReviewTypes) {
            throw new BusinessException('Существуют непроверенные задания, завершение невозможно');
        }
    }
}
