<?php

namespace App\Modules\Attempt\Action;

use App\Modules\Attempt\Action\FinilizeAttemptChecking;
use App\Enums\TaskType;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class FinishAttemptManualCheckingAction
{
    public function __construct(
        protected FinilizeAttemptChecking $finilizeAttemptChecking
    ) {}

    public function execute(Attempt $attempt): Attempt
    {
        $this->ensureNotChecked($attempt);
        $this->ensureAllManualTasksChecked($attempt);
        $attempt = $this->finilizeAttemptChecking
            ->execute($attempt);
        return $attempt;
    }

    protected function ensureNotChecked(Attempt $attempt): void
    {
        if ($attempt->isChecked()) {
            Log::warning('trying to repeat to finish attempt checking', [
                'attempt_id' => $attempt->id,
            ]);
            throw new BusinessException('Попытка уже проверена');
        }
    }

    protected function ensureAllManualTasksChecked(Attempt $attempt): void
    {
        $notAllManualTasksChecked = $attempt->answers()
            ->whereNull('checked_at')
            ->whereHas('taskVariant', function (Builder $query) {
                $query->whereHas('task', function (Builder $q) {
                    $q->whereIn('type', TaskType::manualCheckTypes());
                });
            })
            ->exists();
        if ($notAllManualTasksChecked) {
            throw new BusinessException('Существуют непроверенные задания, завершение невозможно');
        }
    }
}
