<?php

namespace App\Modules\Attempt;

use App\Modules\Attempt\CheckPassingThresholds;
use App\Models\Attempt;

class FinilizeAttemptChecking
{
    public function __construct(
        protected CheckPassingThresholds $checkPassingThresholds
    ) {}

    public function execute(Attempt $attempt): Attempt
    {
        $attempt->total_mark = $attempt->attemptAnswers()->sum('mark');

        $attempt->loadMissing([
            'attemptAnswers.taskVariant.task.subblock', 
            'exam.type.blocks.subblocks'
        ]);

        $attempt->is_passed = $this->checkPassingThresholds->execute($attempt);
        
        $attempt->markAsChecked();
        $attempt->save();

        return $attempt;
    }
}
