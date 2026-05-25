<?php

namespace App\Domain\Attempt\Services;

use App\Exceptions\Subblock\SubblockNotFoundException;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Support\Collection;

class CheckPassingThresholdsService
{
    public function execute(Attempt $attempt): bool
    {
        $answersBySubblock = $attempt->answers
            ->groupBy(function (AttemptAnswer $answer) {
                return $answer->taskVariant->task->subblock_id;
            });

        $subblocks = $this->getSubblocks($attempt);

        return $this->checkPassingThreshold(
            $answersBySubblock,
            $subblocks,
            $attempt
        );

    }

    protected function getSubblocks($attempt): Collection
    {
        $subblocks = $attempt->exam->type->blocks
            ->pluck('subblocks')
            ->flatten();

        return $subblocks;
    }

    protected function checkPassingThreshold(
        Collection $answersBySubblock,
        Collection $subblocks,
        Attempt $attempt
    ): bool {
        foreach ($answersBySubblock as $subblockId => $answers) {
            $subblock = $subblocks->firstWhere('id', $subblockId);
            if (! $subblock) {
                throw new SubblockNotFoundException([
                    'subblock_id' => $subblockId,
                    'attempt_id' => $attempt->id,
                ]);
            }
            $answersSumMark = $answers->sum('mark');
            if ($subblock->min_mark > $answersSumMark) {
                return false;
            }
        }

        return true;
    }
}
