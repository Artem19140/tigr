<?php

namespace App\Modules\AttemptAnswer\Handlers;

use App\Enums\TaskType;
use App\Exceptions\AttemptAnswerValidationException;
use App\Models\Answer;
use App\Models\AttemptAnswer;
use App\Models\TaskVariant;
use Illuminate\Support\Facades\DB;

class SingleChoiceTaskHandler implements AnswerHandlerInterface
{
    public function validate(
        mixed $answerId,
        AttemptAnswer $attemptAnswer
    ): Answer {
        if (! \is_int($answerId)) {
            throw new AttemptAnswerValidationException([
                'attempt_answer_id' => $attemptAnswer->id,
                'type' => TaskType::SingleChoice->value,
                'message' => 'Attempt answer unvalid format',
                'answer' => $answerId
            ]);
        }

        $answers = $attemptAnswer->taskVariant->answers;

        $answer = $answers->firstWhere('id', $answerId);

        if (! $answer) {
            throw new AttemptAnswerValidationException([
                'attempt_answer' => $answerId,
                'type' => TaskType::SingleChoice->value,
                'message' => 'answer not exists on task variant',
            ]);
        }

        return $answer;
    }

    public function calculateMark(
        Answer $answer,
        TaskVariant $taskVariant
    ): int {
        return $answer->is_correct ? $taskVariant->task->mark : 0;
    }

    public function handle(
        mixed $foreignNationalAnswer,
        AttemptAnswer $attemptAnswer
    ): void {
        $answer = $this->validate(
            $foreignNationalAnswer,
            $attemptAnswer
        );

        $mark = $this->calculateMark(
            $answer,
            $attemptAnswer->taskVariant
        );

        $attemptAnswer->update([
            'answer' => [
                'id' =>  $answer->id,
                'order' => $answer->order
            ],
            'answer_id' => $answer->id,
            'mark' => $mark
        ]);
    }

    protected function fd()
    {
        DB::transaction(function(){
            AttemptAnswer::query()
                ->whereHas('taskVariant.task',function($query){
                    $query->where('type', TaskType::SingleChoice);
                })
                ->chunkById(200, function($answers){
                    foreach($answers as $answer){

                        if($answer->answer){
                            $answer->update([
                                'answer_id' => $answer->answer['id']
                            ]);
                        }
                        
                    }
                });
        });
    }
    
}
