<?php

namespace App\Modules\Attempt\Action;

use App\Modules\Attempt\Services\CheckPassingThresholdsService;
use App\Models\Attempt;

class FinilizeAttemptCheckingAction
{
    public function __construct(
        protected CheckPassingThresholdsService $checkPassingThresholdsService
    ) {}

    public function execute(Attempt $attempt): Attempt
    {
        $attempt->total_mark = $attempt->answers()->sum('mark');

        $attempt->loadMissing(['answers.taskVariant.task.subblock', 'exam.type.blocks.subblocks']);
        $attempt->is_passed = $this->checkPassingThresholdsService->execute($attempt);
        $attempt->markAsChecked();
        $attempt->save();

        return $attempt;
    }
}
