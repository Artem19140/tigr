<?php

namespace App\Modules\Attempt\Services;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckPassingThresholds
{
    public function execute(Attempt $attempt): bool
    {

        $blocksPassing = $this->checkBlocksThresholdPassing($attempt);
        
        if(! $blocksPassing){
            return false;
        }

        return  $this->checkSubblocksThresholdPassing($attempt);

    }

    protected function checkBlocksThresholdPassing(Attempt $attempt):bool
    {
        $answersByBlock = $attempt->answers
            ->groupBy(function (AttemptAnswer $answer) {
                return $answer->taskVariant->task->subblock->block_id;
            });
        $blocks = $attempt->exam->type->blocks;
        return $this->checkPassingThreshold(
            $answersByBlock,
            $blocks,
            $attempt
        );
    }

    protected function checkSubblocksThresholdPassing(Attempt $attempt):bool
    {
        $answersBySubblock = $attempt->answers
            ->groupBy(function (AttemptAnswer $answer) {
                return $answer->taskVariant->task->subblock_id;
            });
        $subblocks = $attempt->exam->type->blocks
            ->pluck('subblocks')
            ->flatten();
        return $this->checkPassingThreshold(
            $answersBySubblock,
            $subblocks,
            $attempt
        );
    }
    protected function checkPassingThreshold(
        Collection $answersByItems,
        Collection $items,
        Attempt $attempt
    ): bool {
        foreach ($answersByItems as $itemId => $answers) {
            $item = $items->firstWhere('id', $itemId);
            if (! $item) {
                Log::critical('not found item during tresholds passing counting',[
                    'item_id' => $itemId,
                    'attempt_id' => $attempt->id,
                    'items' => $items
                ]);
                throw new BusinessException('Произошла ошибка во время подсчета результатов');
            }
            $answersSumMark = $answers->sum('mark');
            if ($item->min_mark > $answersSumMark) {
                return false;
            }
        }
        return true;
    }
}
